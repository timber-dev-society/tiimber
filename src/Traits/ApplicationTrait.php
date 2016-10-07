<?php
namespace Tiimber\Traits;
include dirname(__DIR__) . '/Consts/Folder.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Consts/Scopes.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Consts/Http.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Consts/Events.php'; // Tweak for "use const" works. Need better autoload
include dirname(__DIR__) . '/Consts/LogLevel.php'; // Tweak for "use const" works. Need better autoload

use Tiimber\{Memory, Loader, Request};
use const Tiimber\Consts\Folder\{BASE, CONFIG, RESOURCE, CACHE};
use const Tiimber\Consts\Scopes\FOLDER;

use React\Promise\Promise;

use Tiimber\Traits\RouteResolverTrait;

trait ApplicationTrait
{
  use RouteResolverTrait;

  public function chop()
  {
    $explodedClass = explode('\\', self::class);
    (new Loader(reset($explodedClass)));
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
