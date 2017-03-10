<?php

namespace Tiimber\Traits;

use React\EventLoop\Factory;
use React\Socket\Server as Socket;
use React\Http\{Server as Http, Request as ReactRequest, Response as ReactResponse};

use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Tiimber\{Config, Dispatcher, Memory, Renderer};
use Tiimber\Http\{Request, Response, Cookie, Session};
use Tiimber\Traits\{RouteResolverTrait, LoggerTrait};

use const Tiimber\Consts\Scopes\{HTTP, LAYOUT};
use const Tiimber\Consts\Http\{PORT, HOST, CODE, HEADER, DEFAULT_HEADERS};
use const Tiimber\Consts\Events\{ERROR, RENDER, REQUEST, STOP, END, ON, DATA, ES};
use const Tiimber\Consts\LogLevel\{INFO, ERROR as LOG_ERROR};

trait ServerTrait
{
  use LoggerTrait;
  use RouteResolverTrait;

  private $request;

  private $routes;

  public function runHttpServer(callable $callback)
  {
    $this->dispatcher = new Dispatcher();
    $this->routes = Config::get('routes', []);

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

  public function runApp(): callable
  {
    return function (ReactRequest $rRequest, ReactResponse $rResponse) {
      Memory::events()->emit(ON, []);

      $cookie = new Cookie($rRequest, $rResponse);
      $response = new Response($rResponse, $cookie);
      $session = new Session($cookie);
      $request = new Request($rRequest, $session, $cookie);

      try {
        $this->log(INFO, 'new ' . $request->getMethod() . ' request on ' . $request->getPath());
  
        Memory::events()->once(END, function ($content) use ($response, $session) {
          $response->end($content);
          $session->store();
        });

        if ($request->getMethod() === 'POST') {
          $request->on(DATA, function ($data) use ($request, $response, $session) {
            $request->addData($data);
            $this->emitRequest($request, $response);
          });
        } else {
          $this->emitRequest($request, $response);
        }

      } catch (\Throwable $e) {
        $this->log(LOG_ERROR, $e->getMessage());
        $this->log(LOG_ERROR, 'Trace : ' . PHP_EOL . $e->getTraceAsString());
      } catch (\Exception $e) {
        $this->log(LOG_ERROR, $e->getMessage());
        $this->log(LOG_ERROR, 'Trace : ' . PHP_EOL . $e->getTraceAsString());
      }
    };
  }

  private function emitRequest($request, $response)
  {
    $render = new Renderer();
    $route = '';
    try {
      $match = $this->resolve($this->routes, $request->getMethod(), $request->getPath());
      $route = strtolower($match['_route']);

      $this->dispatcher->emit(REQUEST, $route, $render, [
        'request' => $request,
        'args' => $match
      ]);

    } catch (RouteNotFoundException $e) {
       $this->dispatcher->emit(ERROR, '404', $render, [
        'request' => $request,
        'args' => []
      ]);
      Memory::get(HTTP)->set(CODE, 404);

    } catch (\Throwable $e) {
      $render->refresh();
      $this->log(LOG_ERROR, $e->getMessage());
      $this->log(LOG_ERROR, $e->getTraceAsString());
      $this->dispatcher->emit(ERROR, '500', $render, [
        'request' => $request,
        'args' => ['error' => $e]
      ]);
      Memory::get(HTTP)->set(CODE, 500);

    } catch (\Exception $e) {
      $render->refresh();
      $this->log(LOG_ERROR, $e->getMessage());
      $this->log(LOG_ERROR, $e->getTraceAsString());
      $this->dispatcher->emit(ERROR, '500', $render, [
        'request' => $request,
        'args' => ['error' => $e]
      ]);
      Memory::get(HTTP)->set(CODE, 500);
    }

    $layout = Memory::get(LAYOUT)->get('\\Blog\\Layouts\\DefaultLayout');
    Memory::events()->emit(END, ['content' => $render->render($this->resolveLayout(REQUEST . ES . $route))]);
  }

  public function resolveLayout($route)
  {
    $pieces = explode(ES, $route);
    $layouts = [];
    $default;
    foreach (Memory::get(LAYOUT) as $namespace => $layout) {
      if (strpos('Default', $namespace) !== -1) {
        $default = $layout;
      }
      if (!defined($namespace . '::EVENTS')) continue;
      foreach ($layout::EVENTS as $event) {
        $common = array_intersect($pieces, explode(ES, $event));
        if (count($common) > 1) {
          $layouts[count($common)] = $layout;
        }
      }
    }
    if (count($layouts) !== 0) {
      ksort($layouts);
      return end($layouts);
    }
    return $default;
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
