<?php
namespace Tiimber;
include __DIR__ . '/Folder.php'; // Tweak for "use const" works. Need better autoload

use Tiimber\{Config, Dispatcher, Request, Memory};
use const Tiimber\Folder\{SCOPE, BASE, CONFIG, RESOURCE, CACHE, DS};

use Tiimber\Traits\{RouteResolverTrait, FolderResolverTrait};

trait Application
{
  use RouteResolverTrait;
  use FolderResolverTrait;

  private function viewsLoading($namespace, $folder)
  {
    if (is_dir($folder . DS . 'Views')) {
      foreach(glob($folder . DS . 'Views' . DS . '*.php') as $file) {
        $classname = '\\' . $namespace . '\\Views\\' . basename($file, '.php');
        Memory::set('views')->set($classname, new $classname());
      }
    }
  }

  private function layoutsLoading($namespace, $folder)
  {
    if (is_dir($folder . DS . 'Layouts')) {
      foreach(glob($folder . DS . 'Layouts' . DS . '*.php') as $file) {
        $classname = '\\' . $namespace . '\\Layouts\\' . basename($file, '.php');
        Memory::set('layouts')->set($classname, new $classname());
      }
    }
  }

  private function appLoading($namespace)
  {
    $this->viewsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->layoutsLoading($namespace, $this->getBaseDir() . DS . $namespace);
  }

  public function chop()
  {
    $explodedClass = explode('\\', self::class);
    $this->appLoading(reset($explodedClass));
    $request = new Request();
    $routes = Config::get('routes', []);
    $match = $this->resolve($routes, $request->method, $request->url);
    (new Dispatcher())->dispatch(strtolower($request->method . '::' . $routes->get($match['_route'])->event), $request, $match);
  }

  public function setRoot($dir)
  {
    Memory::set(SCOPE)->set(BASE, $dir);
  }

  public function setConfigDir($dir)
  {
    Memory::set(SCOPE)->set(CONFIG, $dir);
  }

  public function setResourceDir($dir)
  {
    Memory::set(SCOPE)->set(RESOURCE, $dir);
  }

  public function setCacheFolder($dir)
  {
    Memory::set(SCOPE)->set(CACHE, $dir);
  }
}
