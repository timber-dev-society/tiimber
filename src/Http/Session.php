<?php
namespace Tiimber\Http;

use Tiimber\{Memory, ParameterBag};
use const Tiimber\Consts\Events\END;
use Tiimber\Traits\LoggerTrait;

class Session
{
  use LoggerTrait;

  private static $instance;

  private function __construct()
  {
    if (isset($_SESSION)) {
      session_start();
    }
  }

  /**
   * Load current session
   *
   * @return Session
   */
  public static function load(string $key): ParameterBag
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    self::$instance->info($key);
    if (self::$instance->has($key)) {
      self::$instance->info(self::$instance->get($key));
      return unserialize(self::$instance->get($key));
    } else {
      return new ParameterBag();
    }
    
  }

  /**
   * Store a value in session
   *
   * @param String $key
   * @param mixed $value
   */
  public static function store($key, $value)
  {
    self::$instance->info(serialize($key));
    self::$instance->info(serialize($value));
    $_SESSION[$key] = serialize($value);
  }

  /**
   * Get parameter stored in session
   *
   * @param String $key
   * @param mixed $default
   * @return mixed
   */
  private function get(string $key, $default = null)
  {
    $this->info(session_id());
    return $_SESSION[$key] ?? $default;
  }

  /**
   * Has parameter stored in session
   *
   * @param String $key
   * @return mixed
   */
  private function has(string $key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Store a value in session
   *
   * @param String $key
   * @param mixed $value
   */
  private function set(string $key, $value): Session
  {
    $_SESSION[$key] = $value;

    return $this;
  }

  /**
   * Remove a stored value in session
   *
   * @param $key
   */
  private function destruct(string $key): Session
  {
    unset($_SESSION[$key]);

    return $this;
  }
}
