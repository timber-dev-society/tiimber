<?php

namespace Tiimber\Injection;

use Tiimber\Bags\ImmutableBag;

abstract class AutoInjected implements InjectedInterface
{
  private $dependecies;

  public final function inject(array $dependencies)
  {
    $this->dependecies = new ImmutableBag($dependecies);
  }

  public final function get(string $dependency)
  {
    return $this->dependecies->get($dependency);
  }
}
