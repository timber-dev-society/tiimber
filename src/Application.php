<?php
namespace Tiimber;

use Tiimber\Config;
use Tiimber\Controller;

class Application
{
  private static $instance;

  private $dir = false;

  private $resource_dir = false;

  private $config_dir = false;

  public function __construct()
  {
    self::$instance = $this;
  }

  public function setRoot($dir)
  {
    if (!$this->dir) {
      $this->dir =  $dir;
    }
  }

  public function chop()
  {
    $routes = Config::get('routes', []);
    new Controller($routes);
  }

  public function setConfigDir($dir)
  {
    if (!$this->config_dir) {
      $this->config_dir =  $dir;
    }
  }

  public function setResourceDir($dir)
  {
    if (!$this->resource_dir) {
      $this->resource_dir =  $dir;
    }
  }

  public static function getBaseDir()
  {
    return self::$instance->dir;
  }

  public static function getConfigDir()
  {
    return self::$instance->config_dir ?: self::$instance->dir . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
  }

  public static function getResourceDir()
  {
    return self::$instance->resource_dir ?: self::$instance->dir . DIRECTORY_SEPARATOR .'Resources' . DIRECTORY_SEPARATOR;
  }
}