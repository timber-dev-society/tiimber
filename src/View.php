<?php
namespace Tiimber;

use Serializable;

use Tiimber\ViewException;

abstract class View implements Serializable
{
  /**
   * Serialize all parameters
   *
   * return string
   */
  public function serialize(): string
  {
    return self::class . '::EVENTS';
  }

  /**
   * unserialize all parameters and return a ParameterBag
   *
   * @param $data string
   * @return View
   */
  public function unserialize($serialized): View
  {
    return $this;
  }

  public function raise(int $code, string $message = null)
  {
    throw new ViewException($message, $code);
  }

  public function dispatch(string $event, ...$args)
  {

  }

  public function __toString()
  {
    return slef::TPL;
  }
}
