<?php
namespace Tiimber;

use Tiimber\Action;

use Tiimber\Exceptions\ViewException;
use Tiimber\Interfaces\RenderableInterface;

abstract class View extends Action implements RenderableInterface
{
  /**
   * unserialize all parameters and return a View
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
  
  public function render(): array
  {
    return [];
  }
}
