<?php
namespace Tiimber\Reducer;

use Tiimber\Interfaces\ReducerInterface;

abstract class Reducer
{
  public final function __invoke($state, $action)
  {
    return $this->onAction($state, $action);
  }
}