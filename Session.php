<?php

namespace KissPHP;

class Session
{
  private static $instance;

  private function __construct()
  {
    session_start();
  }

  public static function load()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function destruct($key)
  {
    unset($_SESSION[$key]);
  }
}