<?php
namespace Tiimber;

use Tiimber\Config;
use Tiimber\Handler;
use Tiimber\Request;
use Tiimber\RouteResolver;
use Tiimber\Memory;
use Tiimber\Folder;

class Application
{
  private static $instance;

  public function __construct()
  {
    self::$instance = $this;
  }

  public function chop()
  {
    $routes = Config::get('routes', []);
    $request = new Request();
    (new RouteResolver($routes, $request));
    (new Handler($request));
  }

  public function setRoot($dir)
  {
    Memory::set(Folder::SCOPE)->set(Folder::BASE, $dir);
  }

  public function setConfigDir($dir)
  {
    Memory::set(Folder::SCOPE)->set(Folder::CONFIG, $dir);
  }

  public function setResourceDir($dir)
  {
    Memory::set(Folder::SCOPE)->set(Folder::RESOURCE, $dir);
  }

  public function setCacheFolder($dir)
  {
    Memory::set(Folder::SCOPE)->set(Folder::CACHE, $dir);
  }
}
