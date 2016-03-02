<?php

namespace KissPHP;

class Session
{
  private static $instance;

  private function __construct()
  {
    session_start();
  }

  /**
   * Load current session
   *
   * @return Session
   */
  public static function load()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Get parameter stored in session
   *
   * @param String $key
   * @param mixed $default
   * @return mixed
   */
  public function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  /**
   * Store a value in session
   *
   * @param String $key
   * @param mixed $value
   */
  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Remove a stored value in session
   *
   * @param $key
   */
  public function destruct($key)
  {
    unset($_SESSION[$key]);
  }
}