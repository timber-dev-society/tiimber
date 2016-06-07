<?php
namespace Tiimber;

use Tiimber\Config;
use Tiimber\Handler;
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
    (new Handler(Config::get('routes', [])));
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
