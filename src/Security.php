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

  public function __get($property)
  {
    if (in_array($property, static::$accessibleProperties)) {
      return $this->{$property};
    }
    return null;
  }

  public static function load()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function setUser($user)
  {
    if ($user instanceof UserSecurityInterface) {
      $this->user = $user;
      Session::load()->set(static::SESSION_ID, $user->getIdentifier());
      return true;
    }

    return false;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function getUserInfos()
  {
    $infos['isAuthenticated'] = $this->isAuthenticated;
    $infos['role'] = $this->user->getRole();
    $infos['username'] = $this->user->getUsername();
    return $infos;
  }

  public function setSecurityDefinition($security)
  {
    $this->refresh();
    $this->autorize($security->role);

    return $this;
  }

  public function authenticate($request)
  {
    $namespace = $this->config->user_table_namespace;
    $table = new $namespace();
    if (!$table instanceof SecurityProviderInterface) {
      throw new Exception($namespace . ' must implement Tiimber\Interfaces\SecurityProviderInterface');
    }
    $user = $table->loadUserByUsernamePassword($request->post->login, self::hashPassword($request->post->password));

    return $this->setUser($user);
  }

  public function logout()
  {
    if (Session::load()->get(static::SESSION_ID, false)) {
      Session::load()->destruct(static::SESSION_ID);
    }
  }

  private function refresh()
  {
    if (Session::load()->get(static::SESSION_ID, false)) {
      $namespace = $this->config->user_table_namespace;
      $table = new $namespace();
      if (!$table instanceof SecurityProviderInterface) {
        throw new Exception($namespace . ' must implement Tiimber\Interfaces\SecurityProviderInterface');
      }
      $this->user = $table->loadUserByIdentifier(Session::load()->get(static::SESSION_ID));
      $this->isAuthenticated = ($this->user !== null);
    }
  }

  public function isAuthenticated($role = false)
  {
    if ($role) {
      $this->autorize($role);
      return $this->isAuthenticated && $this->isAuthorized;
    }

    return $this->isAuthenticated;
  }

  public static function hashPassword($password)
  {
    return sha1($password . self::load()->config->salt);
    //return password_hash($password, PASSWORD_BCRYPT, ['salt' => self::load()->config->salt]);
  }

  private function autorize($role)
  {
    if ($this->isAuthenticated) {
      $this->isAuthorized = $this->user->hasRole($role);
    }
  }
}