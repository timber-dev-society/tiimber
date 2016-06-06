<?php
namespace Tiimber;

use Spot\Config as SpotConfig;
use Spot\Locator;

use Tiimber\Config;
use Tiimber\ParameterBag;
use Tiimber\Exception;

class Database
{
  private $spots;

  private static $instance;

  private function __construct()
  {
    this->config = Config::get('database');
  }

  private function retrieveConfig(string $name = null): ParameterBag
  {
    if (is_null($name)) {
      if ($this->config->has('dbname')) {
        $config = $this->config;
      } elseif ($this->config->has('default')) {
        $config = $this->config->get('default');
      } else {
        foreach($this->config as $config) {
          break;
        }
      }
    } else {
      if (!$this->config->has($name)) {
        throw new Exception('No configuration found for database: ' . $name, 500);
      }
      $config = $this->config->get($name)
    }
    return $config;
  }

  private function getSpotInstance(string $name = null): Locator
  {
    if (!array_key_exists[$name, $this->spots]) {
      $config = $this->retrieveConfig($name);
      $spotConfig = SpotConfig();
      $spotConfig->addConnection(
        $config->provider ?: 'mysql',
        [
          'dbname' => $config->dbname,
          'user' => $config->user ?: 'root',
          'password' => $config->password ?: '',
          'host' => $config->host ?: 'localhost',
          'driver' => $config->driver ?: 'pdo_mysql',
        ]
      )
      $this->spots[$name] = new Locator($spotConfig);
    }

    return $this->spots[$name]
  }

  public static function connect(string $name = null): Locator
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance->getSpotInstance($name);
  }
}
