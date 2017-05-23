<?php
namespace Tiimber\Reducer;

use Tiimber\Interfaces\ReducerInterface;

abstract class Reducer
{
  protected $state;

  private $nextState;

  public final function init($state = null)
  {
    if ($state instanceof ReducerInterface) {
      $this->state = $state->getState();
    } elseif ($state !== null) {
      $this->state = new ImmutableBag($state);
    } else {
      $this->state = new ImmutableBag($this->defaultState);
    }
  }

  private final function cleanAction($action)
  {
    if (isset($action['type'])) {
      unset($action['type']);
    }
    return $action;
  }

  protected function mutateState(): array
  {
    $state = [];
    foreach($this->state as $key => $value) {
      $state[$key] = $value;
      if (isset($this->nextState[$key])) {
        $state[$key] = $this->nextState[$key];
        unset($this->nextState[$key]);
      }
    }
    if (count($this->nextState) !== 0) {
      foreach($this->nextState as $key => $value) {
        $state[$key] = $value;
      }
    }
    return $state;
  }

  protected final function __clone()
  {
    $this->state = new ImmutableBag($this->mutateState());
    $this->nextState = null;
  }

  public final function getState()
  {
    return $this->state;
  }


  public final function __invoke($state, $action)
  {
    $this->init();
    $this->onAction($state, $action);
    return clone $this;
  }
}