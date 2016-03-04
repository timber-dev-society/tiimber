<?php
namespace Tiimber;

use PDO;
use Tiimber\Config;
use Tiimber\Exception;

class Sql
{
  public $connection;

  private static $instance;

  private $config;

  private function __construct()
  {
    $optn = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    $this->config = Config::get('database');
    try {
      $this->connection = new PDO('mysql:host=' . $this->config->host . ';dbname=' . $this->config->dbname . ';charset=' . $this->config->charset, $this->config->login, $this->config->password, $optn);
    } catch (\Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function connect()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __call($method, $arguments)
  {
    try {
      return call_user_func_array([$this->connection, $method], $arguments);
    } catch (\Exception $e) {
      throw new Exception($e->getMessage());
    }
  }
}