<?php

namespace Tiimber;

use Serializable;

use Tiimber\Exceptions\ActionException;
use Tiimber\Interfaces\ActionInterface;
use Tiimber\Http\{Request, Response};

abstract class Action implements Serializable, ActionInterface
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

  public function onGet(Request $req, Response $res)
  {
    return;
  }

  public function onPost(Request $req, Response $res)
  {
    return;
  }

  public function onCall(Request $req, Response $res)
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