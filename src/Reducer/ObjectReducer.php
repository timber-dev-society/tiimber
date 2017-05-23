<?php
namespace Tiimber\Reducer;

use Tiimber\Reducer\Reducer;

class ObjectReducer extends Reducer
{
  public $defaultState = [];

  public function __construct($action = null)
  {
    if ($action !== null) {
      $action = $this->cleanAction($action);
      $this->state = new ImmutableBag($action);
    }
  }

  protected final function assign($params)
  {
    $this->nextState = $this->cleanAction($params);
    return $this;
  }

  public function __get($property)
  {
    return $this->state->get($property);
  }
}