<?php
namespace Tiimber;

use Tiimber\ParameterBag;
use Tiimber\Memory\ProviderResolver;

class Memory
{
  private static $instance;

  private $scopes;

  private function __construct()
  {
    $this->scopes = new ParameterBag();
  }

  private static function init(): Memory
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function get(string $scope): ParameterBag
  {
    $instance = self::init();
    return $instance->scopes->get($scope);
  }

  public function set(string $scope): ParameterBag
  {
    $instance = self::init();
    if (!$instance->scopes->get($scope, false)) {
      $instance->scopes->set($scope, new ParameterBag());
    }
    return $instance->scopes->get($scope);
  }

  public function memorize(string $scope, string $provider = 'session')
  {
    $instance = self::init();
    $resolver = new ProviderResolver();
    $provider = $resolver->resolve($provider);
    $provider->store($instance->scopes->get($scope));
  }
}
