<?php
namespace Tiimber;

use Tiimber\ParameterBag;
use Tiimber\Memory\ProviderResolver;
use Tiimber\Exceptions\Exception;

use Evenement\EventEmitterTrait;

class Memory
{
  private static $instance;

  private $scopes;
  
  private $events;

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

  public static function get(string $scope): ParameterBag
  {
    $instance = self::init();
    if (!$instance->scopes->has($scope)) {
      throw new Exception('Uninitialize memory for ' . $scope . ' scope', 500);
    }
    return $instance->scopes->get($scope);
  }

  public static function set(string $scope): ParameterBag
  {
    $instance = self::create($scope);
    
    return $instance->scopes->get($scope);
  }
  
  public static function create(string $scope): Memory
  {
    $instance = self::init();
    if (!$instance->scopes->has($scope)) {
      $instance->scopes->set($scope, new ParameterBag());
    }
    return $instance;
  }
  
  public static function events()
  {
    $instance = self::init();
    if (!$instance->events) {
      $instance->events = new class {
        use EventEmitterTrait;
      };
    }
    return $instance->events;
  }

  public function memorize(string $scope, string $provider = 'session')
  {
    $instance = self::init();
    $resolver = new ProviderResolver();
    $provider = $resolver->resolve($provider);
    $provider->store($instance->scopes->get($scope));
  }
}
