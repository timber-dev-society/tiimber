<?php
namespace KissPHP;

use PDO;

class Sql
{
  public $connection;

  private static $instance;

  private function __construct()
  {
    $optn = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try {
      $this->connection = new PDO('mysql:host=localhost;dbname=takaclic;charset=utf8', 'dious', '', $optn);
    } catch (\Exception $e) {
      echo 'Connection impossible : ', $e->getMessage();
      die();
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
    return call_user_func_array([$this->connection, $method], $arguments);
  }
}