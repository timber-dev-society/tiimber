<?php
namespace Tiimber;

use Tiimber\Action;

use Tiimber\Exceptions\ViewException;
use Tiimber\Interfaces\RenderableInterface;

abstract class View
{
  public function raise(int $code, string $message = null)
  {
    throw new ViewException($message, $code);
  }

  public function propsToState(array $props, array $ownProps): array
  {
    return $ownProps;
  }
  
  public function render(array $state): array
  {
    return $state;
  }
}
