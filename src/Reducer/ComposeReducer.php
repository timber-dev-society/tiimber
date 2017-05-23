<?php
namespace Timmber\Reducer;

class ComposedReducer extends CallableReducer
{
    protected $reducers = [];

    public function __construct(array $reducers = [])
    {
      foreach ($reducers as $key => $reducer) {
        $this->addReducer($key, $reducer);
      }
    }

    public function reduce($state, array $action)
    {
      foreach ($this->reducers as $key => $reducer) {
        $state[$key] = $reducer($state[$key], $action);
      }

      return $state;
    }
}