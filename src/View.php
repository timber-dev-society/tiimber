<?php
namespace Tiimber;

use Tiimber\Action;

use Tiimber\Exceptions\ViewException;

abstract class View extends Action
{
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

  public function __toString()
  {
    return self::TPL;
  }
  
  public function render()
  {
    return [];
  }
}
