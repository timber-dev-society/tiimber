<?php
namespace KissPHP;

use KissPHP\Config;
use KissPHP\Controller;

class Application
{
  private static $instance;

  private $dir = false;

  public function __construct()
  {
    self::$instance = $this;
  }

  public function setBaseDir($dir)
  {
    if (!$this->dir) {
      $this->dir =  $dir;
    }
  }

  public function start()
  {
    $routes = Config::get('routes', []);
    new Controller($routes);
  }

  public function getBaseDir()
  {
    return self::$instance->dir;
  }
}