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


trait ApplicationTrait
{
  public function chop(string $app = null)
  {
    if ($app === null) {
      $explodedClass = explode('\\', self::class);
      $app = reset($explodedClass);
    }
    (new Loader($app));
  }

  public function setRoot(string $dir)
  {
    Memory::set(FOLDER)->set(BASE, $dir);
  }

  public function setConfigDir(string $dir)
  {
    Memory::set(FOLDER)->set(CONFIG, $dir);
  }

  public function setResourceDir(string $dir)
  {
    Memory::set(FOLDER)->set(RESOURCE, $dir);
  }

  public function setCacheFolder(string $dir)
  {
    Memory::set(FOLDER)->set(CACHE, $dir);
  }
}
