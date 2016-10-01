<?php
namespace Tiimber\Traits;
include dirname(__DIR__) . '/Folder.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Memory/Scopes.php'; // Tweak for "use const" works. Need better autoload

use Tiimber\{Config, Dispatcher, Request, Memory, Loader};
use const Tiimber\Folder\{BASE, CONFIG, RESOURCE, CACHE};
use const Tiimber\Memory\Scopes\FOLDER;

use Tiimber\Traits\RouteResolverTrait;

trait ApplicationTrait
{
  use RouteResolverTrait;

  public function chop()
  {
    $explodedClass = explode('\\', self::class);
    (new Loader(reset($explodedClass)));
    $request = new Request();
    $routes = Config::get('routes', []);
    $match = $this->resolve($routes, $request->method, $request->url);
    (new Dispatcher())->dispatch(strtolower('request::' . $match['_route']), $request, $match);
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
