<?php

namespace Tiimber\Injection;

interface InjectedInterface
{
  public function require():array;

  public function initialize();
}
