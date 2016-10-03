<?php
namespace Tiimber\Traits;
include dirname(__DIR__) . '/Folder.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Memory/Scopes.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Memory/Http.php'; // Tweak for "use const" works. Need better autoload

use Tiimber\{Config, Dispatcher, Request, Memory, Loader};
use const Tiimber\Folder\{BASE, CONFIG, RESOURCE, CACHE};
use const Tiimber\Memory\Scopes\{FOLDER, HTTP};
use const Tiimber\Memory\Http\{PORT, HOST};

use React\EventLoop\Factory;
use React\Socket\Server as Socket;
use React\Http\Server as Http;

use Tiimber\Traits\RouteResolverTrait;

trait ApplicationTrait
{
  use RouteResolverTrait;

  public function chop()
  {
    $explodedClass = explode('\\', self::class);
    (new Loader(reset($explodedClass)));
    // $request = new Request();
    $this->startHttpServer();
  }
  
  public function getApp()
  {
    return function ($request, $response) {
      $routes = Config::get('routes', []);
      $match = $this->resolve($routes, $request->getMethod(), $request->getPath());
      $value = (new Dispatcher())->dispatch(strtolower('request::' . $match['_route']), $request, $match);
      $response->end($value);
    };
  }
  
  public function startHttpServer()
  {
    $loop = Factory::create();
    $socket = new Socket($loop);
    $http = new Http($socket);
    $http->on('request', $this->getApp());
    Memory::create(HTTP);
    $socket->listen(Memory::get(HTTP)->get(PORT, '1337'), Memory::get(HTTP)->get(HOST, '127.0.0.1'));
    $loop->run();
  }

  public function setHost(string $host)
  {
    Memory::set(HTTP)->set(HOST, $host);
  }

  public function setPort(int $port)
  {
    Memory::set(HTTP)->set(PORT, $port);
  }

  public function setRoot($dir)
  {
    Memory::set(FOLDER)->set(BASE, $dir);
  }

  public function setConfigDir($dir)
  {
    Memory::set(FOLDER)->set(CONFIG, $dir);
  }

  public function setResourceDir($dir)
  {
    Memory::set(FOLDER)->set(RESOURCE, $dir);
  }

  public function setCacheFolder($dir)
  {
    Memory::set(FOLDER)->set(CACHE, $dir);
  }
}
