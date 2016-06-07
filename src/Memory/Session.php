<?php

namespace Tiimber\Memory;

use Tiimber\ParameterBag;
use Tiimber\Session as BaseSession;
use Tiimber\Interfaces\MemoryProviderInterface;

class Session implements MemoryProviderInterface
{
  public function store(string $scope, ParameterBag $values)
  {
    BaseSession::load()->set($scope, $values->serialize());
  }

  public function restore(string $scope): ParameterBag
  {
    return (new ParameterBag())->unserialize(BaseSession::load()->get($scope));
  }
}
