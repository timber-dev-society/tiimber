<?php

namespace Tiimber;

use Serializable;

use Tiimber\Exceptions\ActionException;

abstract class Action implements Serializable
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
  public function unserialize($serialized): Action
  {
    return $this;
  }
  
  public function raise(int $code, string $message = null)
  {
    throw new ActionException($message, $code);
  }

  public function dispatch(string $event, ...$args)
  {

  }
}