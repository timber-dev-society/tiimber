<?php

namespace Tiimber\Traits;

use React\EventLoop\Factory;
use React\Socket\Server as Socket;
use React\Http\{Server as Http, Request as ReactRequest, Response as ReactResponse};

use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Tiimber\{Config, Dispatcher, Memory, Renderer, Renderer\Pages};
use Tiimber\Http\{Request, Response, Cookie, Session, QueryParser};
use Tiimber\Traits\{RouteResolverTrait, LoggerTrait};

use const Tiimber\Consts\Scopes\{HTTP, LAYOUT};
use const Tiimber\Consts\Http\{PORT, HOST, CODE, HEADER, DEFAULT_HEADERS};
use const Tiimber\Consts\Events\{ERROR, RENDER, REQUEST, STOP, END, ON, DATA, ES};
use const Tiimber\Consts\LogLevel\{INFO, ERROR as LOG_ERROR};

trait ServerTrait
{
  use LoggerTrait;
  use RouteResolverTrait;
  
  private static $start;

  private $pages;

  private $routes;

  public function runHttpServer(callable $callback)
  {
    $this->dispatcher = new Dispatcher();
    $this->routes = Config::get('routes', []);
    $this->pages = new Pages($this->routes);

    $loop = Factory::create();
    $socket = new Socket($loop);
    $http = new Http($socket);
    $http->on(REQUEST, $callback);

    Memory::create(HTTP);
    $socket->listen(
      Memory::get(HTTP)->get(PORT, '1337'),
      Memory::get(HTTP)->get(HOST, '127.0.0.1')
    );
    $loop->run();
  }

  private function initApp(ReactRequest $rRequest, ReactResponse $rResponse)
  {
    $cookie = new Cookie($rRequest, $rResponse);
    $response = new Response($rResponse, $cookie);
    $session = new Session($this->getSessid($cookie));
    $request = new Request($rRequest, $session, $cookie);

    Memory::events()->once(END, function ($content) use ($response, $session) {
      $session->store();
      $response->end($content);
      $this->info('Response delivred in ' . round((microtime(true) - self::$start) * 1000, 2) . 'ms');
    });

    return [$request, $response];
  }

  /**
   * get current sessid
   *
   * @param $cookie Cookie
   * @return string
   */
  private function getSessid(Cookie $cookie): string
  {
    if (!$cookie->has('sessid')) {
      $sessid = uniqid('tiim', true);
    } else {
      $sessid = $cookie->get('sessid');
    }
    $cookie->add('sessid', $sessid, time() + 3600);
    $this->info('sessid: ' . $sessid);

    return $sessid;
  }

  public function runApp(): callable
  {
    return function (ReactRequest $rRequest, ReactResponse $rResponse) {
      
      self::$start = microtime(true);
      Memory::events()->emit(ON, []);
      list($request, $response) = $this->initApp($rRequest, $rResponse);

      try {
        $this->log(INFO, 'new ' . $request->getMethod() . ' request on ' . $request->getPath());

        if ($rRequest->getMethod() === 'POST') {
          $rRequest->on(DATA, function ($data) use ($request, $response) {
            $this->emitRequest($request->setData(QueryParser::parse($data)), $response);
          });
        } else {
          $this->emitRequest($request, $response);
        }

      } catch (\Throwable $e) {
        return $this->emitError($e, 500, $request, $response);
      } catch (\Exception $e) {
        return $this->emitError($e, 500, $request, $response);
      }
    };
  }

  private function emitError($error, int $code, Request $request, Response $response)
  {
    if (!$error instanceof RouteNotFoundException) {
      $this->log(LOG_ERROR, $error->getMessage());
      $this->log(LOG_ERROR, $error->getTraceAsString());
    }

    $render = new Renderer();
    $request = $request->clone(['error' => $error]);

    $this->dispatcher->emit(ERROR, $code, $render, [
      'req' => $request,
      'res' => $response
    ]);
    Memory::events()->emit(END, ['content' => $render->render($this->pages->resolveErrorLayout(ERROR . ES . $code))]);
  }

  private function emitRequest($request, $response)
  {
    try {
      $render = new Renderer();

      $match = $this->resolve($this->routes, $request->getMethod(), $request->getPath());
      $route = strtolower($match['_route']);
      unset($match['_route']);
      $request->setArgs($match);

      $this->dispatcher->emit(REQUEST, $route, $render, [
        'req' => $request,
        'res' => $response
      ]);

      Memory::events()->emit(END, ['content' => $render->renderPage($this->pages->resolve(REQUEST . ES . $route))]);

    } catch (RouteNotFoundException $e) {
      return $this->emitError($e, 404, $request, $response);
    }
  }

  public function setHost(string $host)
  {
    Memory::set(HTTP)->set(HOST, $host);
  }

  public function setPort(int $port)
  {
    Memory::set(HTTP)->set(PORT, $port);
  }
}
