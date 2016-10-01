<?php
namespace Tiimber;

use Tiimber\Interfaces\UserSecurityInterface;
use Tiimber\Interfaces\SecurityProviderInterface;

use Tiimber\Config;
use Tiimber\Session;

class Security
{
  private $isAuthenticated = false;

  private $isAuthorized = false;

  private $user;

  private $config;

  private static $instance;

  private static $accessibleProperties = [
    'isAuthenticated',
    'isAuthorized'
  ];

  const SESSION_ID = 'user_identifier';

  private function __construct()
  {
    $this->config = Config::get('security');
    $this->refresh();
  }

  public function __get(string $property): bool
  {
    if (in_array($property, static::$accessibleProperties)) {
      return $this->{$property};
    }
    return false;
  }

  public static function load(): Security
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function setUser(UserSecurityInterface $user): bool
  {
    if ($user instanceof UserSecurityInterface) {
      $this->user = $user;
      Session::load()->set(static::SESSION_ID, $user->getIdentifier());
      return true;
    }

    return false;
  }

  public function getUser(): UserSecurityInterface
  {
    return $this->user;
  }

  public function setSecurityDefinition($security): Security
  {
    $this->refresh();
    $this->autorize($security->role);

    return $this;
  }

  public function authenticate($parameter): bool
  {
    $provider = $this->getSecurityProvider();
    $user = $provider->loadUser($parameter);

    return $this->setUser($user);
  }

  public function logout()
  {
    if (Session::load()->get(static::SESSION_ID, false)) {
      Session::load()->destruct(static::SESSION_ID);
    }
  }

  private function getSecurityProvider(): SecurityProviderInterface
  {
    $namespace = $this->config->get('security_provider');
    $provider = new $namespace();
    if (!$provider instanceof SecurityProviderInterface) {
      throw new Exception($namespace . ' must implement Tiimber\Interfaces\SecurityProviderInterface');
    }
    return $provider;
  }

  private function refresh()
  {
    if (Session::load()->get(static::SESSION_ID, false)) {
      $provider = $this->getSecurityProvider();
      $this->user = $provider->loadUserByIdentifier(Session::load()->get(static::SESSION_ID));
      $this->isAuthenticated = ($this->user !== null);
    }
  }

  public function isAuthenticated(string $role = null): bool
  {
    if (!is_null($role)) {
      $this->autorize($role);
      return $this->isAuthenticated && $this->isAuthorized;
    }

    return $this->isAuthenticated;
  }

  private function autorize(string $role)
  {
    if ($this->isAuthenticated) {
      $this->isAuthorized = $this->user->hasRole($role);
    }
  }
}
