<?php

namespace Tiimber\Memory;

use Tiimber\ParameterBag;
use Tiimber\Session as BaseSession;

class Session
{
  public function store(string $scope, ParameterBag $values)
  {
    BaseSession::load()->set($scope, $values->serialize);
  }

  public function restore(string $scope): ParameterBag
  {
    retrun (new ParameterBag())->unserialize(BaseSession::load()->get($scope));
  }
}
