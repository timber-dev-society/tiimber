<?php
namespace Tiimber;

use Tiimber\Action;

use Tiimber\Exceptions\ViewException;
use Tiimber\Interfaces\RenderableInterface;

abstract class View implements RenderableInterface
{
  public function raise(int $code, string $message = null)
  {
    throw new ViewException($message, $code);
  }

  public function stateToProps($state, $props): array
  {
    return [];
  }
  
  public function render(): array
  {
    return [];
  }
}
