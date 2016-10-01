<?php

namespace Tiimber;

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
  public static function load(): Session
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
  public function get(string $key, $default = null)
  {
    return $_SESSION[$key] ?? $default;
  }

  /**
   * Has parameter stored in session
   *
   * @param String $key
   * @return mixed
   */
  public function has(string $key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Store a value in session
   *
   * @param String $key
   * @param mixed $value
   */
  public function set(string $key, $value): Session
  {
    $_SESSION[$key] = $value;

    return $this;
  }

  /**
   * Remove a stored value in session
   *
   * @param $key
   */
  public function destruct(string $key): Session
  {
    unset($_SESSION[$key]);

    return $this;
  }
}
