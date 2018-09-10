<?php

namespace Tiimber\Injection;

trait InjectedTrait
{
  private $dependecies;

  function require(array $dependecies)
  {
    $this->dependecies = new ImmutableBag($dependecies);
  }

  public function get(string $dependency)
  {
    return $this->dependecies->get($dependency);
  }
}
