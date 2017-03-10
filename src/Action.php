<?php

namespace Tiimber;

use Serializable;

use Tiimber\Exceptions\ActionException;
use Tiimber\Interfaces\EventInterface;
use Tiimber\Http\Request;

abstract class Action implements Serializable, EventInterface
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

  public function onGet(Request $request, array $args)
  {
    return;
  }

  public function onPost(Request $request, array $args)
  {
    return;
  }

  public function onCall(Request $request, array $args)
  {
    return;
  }
  
  public function raise(int $code, string $message = null)
  {
    throw new ActionException($message, $code);
  }

  public function dispatch(string $event, ...$args)
  {

  }
}