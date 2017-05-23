<?php
namespace Tiimber\Traits;

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
