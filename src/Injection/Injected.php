<?php

namespace Tiimber\Injection;

use Tiimber\Bags\ImmutableBag;

abstract class Injected implements InjectedInterface
{
  private $dependecies;

  public function inject(array $dependencies)
  {
    $this->dependecies = new ImmutableBag($dependecies);
  }
}