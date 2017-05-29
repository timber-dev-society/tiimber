<?php
namespace Tiimber\Reducer;

use Tiimber\Reducer\Reducer;

class CollectionReducer
{
  public $defaultState = [];

  const DELETE = 'TIIMBER_COLLECTION_REDUCER_DELETE_ROW';

  protected final function map($callback)
  {
    $state = [];
    foreach ($this->state as $key => $value) {
      $newValue = $callback($value, $key);
      if ($newValue !== static::DELETE) {
        $state[$key] = $newValue;
      }
    }
    return $state;
  }
}
