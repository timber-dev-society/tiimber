<?php
namespace Tiimber;

use Serializable;

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
}
