<?php
namespace Tiimber;

use InvalidArgumentException;

use Tiimber\Interfaces\ReducerInterface;
use Tiimber\Exceptions\{MissingTypeException, StoreRollback};
use Tiimber\Bags\ObservableBag;

class Store
{
  private $state = [];
  private $listeners = [];
  private $nextState = [];
  private $store;

  public function __construct()
  {

  }

  public function create(ReducerInterface $store)
  {
    $this->store = new $store();
  }

  public function getState(): array
  {
    return $this->state;
  }

  public function listen(callable $listener)
  {
    $this->listeners[] = $listener;

    $index = count($this->listeners) - 1;
    $listeners = $this->listeners;
    $unsubscribe = function () use ($index, &$listeners) {
      array_splice($listeners, $index, 1);
    };

    return $unsubscribe;
  }

  protected function execDispatch(array $action)
  {
    if (!isset($action['type'])) {
      throw new MissingTypeException();
    }
    if (!is_array($action)) {
      throw new InvalidArgumentException('Action must be an array');
    }

    $this->nextState = $this->store($this->$state, $action);
    try {
      foreach ($this->listeners as $listener) {
        $listener($this->state, $this->nextState);
      }
    } catch (StoreRollback $error) {
      $this->nextState = $this->state;
    }
    
    $this->state = $this->nextState;
    $this->nextState = [];
  }

  public function dispatch(): callable
  {
    $self = $this;
    return function (array $action) use ($self) {
      $self->execDispatch($action);
    };
  }
}