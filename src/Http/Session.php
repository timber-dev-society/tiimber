<?php
namespace Tiimber\Http;

use Tiimber\{Memory, ParameterBag, Http\Cookie};
use const Tiimber\Consts\Events\END;
use Tiimber\Traits\LoggerTrait;

class Session
{
  use LoggerTrait;

  private $sessid;

  private $bag;

  public function __construct(string $sessid)
  {
    $this->sessid = $sessid;
    $this->bag = $this->load();
  }

  /**
   * Load current session
   *
   * @return Session
   */
  private function load(): ParameterBag
  {
    if (isset($_SESSION[$this->sessid])) {
      return unserialize($_SESSION[$this->sessid]);
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
  public function store()
  {
    $_SESSION[$this->sessid] = serialize($this->bag);
  }

  /**
   * Destruct current session
   *
   * @param $key
   */
  public function destruct()
  {
    $this->bag = new ParameterBag();
    $this->store();
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
    return $this->bag->get($key, $default);
  }

  /**
   * Has parameter stored in session
   *
   * @param String $key
   * @return mixed
   */
  public function has(string $key): bool
  {
    return $this->bag->has($key);
  }

  /**
   * Store a value in session
   *
   * @param String $key
   * @param mixed $value
   */
  public function set(string $key, $value)
  {
    $this->bag->set($key, $value);
  }

  /**
   * Untore a value in session
   *
   * @param String $key
   */
  public function unset(string $key)
  {
    $this->bag->unset($key);
  }
}
