<?php

namespace Tiimber\Memory;

use Tiimber\Bags\SerializableBag;
use Tiimber\Session as BaseSession;
use Tiimber\Interfaces\MemoryProviderInterface;

class Session implements MemoryProviderInterface
{
  public function store(string $scope, SerializableBag $values)
  {
    BaseSession::load()->set($scope, $values->serialize());
  }

  public function restore(string $scope): SerializableBag
  {
    return (new ParameterBag())->unserialize(BaseSession::load()->get($scope));
  }
}
