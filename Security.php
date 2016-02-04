<?php
namespace KissPHP;

use KissPHP\Tables\Users as UserTable;
use KissPHP\Models\User;

use KissPHP\Config;
use KissPHP\Session;

class Security
{
  public $isAuthenticated = false;

  public $isAuthorized = false;

  private $user;

  private $config;

  private static $instance;

  private function __construct()
  {
    $this->config = Config::get('security');
    $this->refresh();
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
    if ($user) {
      $this->user = $user;
      Session::load()->set('user_id', $user->id);
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
    $infos['role'] = $this->user->__get('role');
    $infos['username'] = $this->user->__get('username');
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
    $user = $table->findOneBy(['username' => $request->post->login, 'password' => self::hashPassword($request->post->password)]);

    return $this->setUser($user);
  }

  public function logout()
  {
    if (Session::load()->get('user_id', false)) {
      Session::load()->destruct('user_id');
    }
  }

  private function refresh()
  {
    if (Session::load()->get('user_id', false)) {
      $namespace = $this->config->user_table_namespace;
      $table = new $namespace();
      $this->user = $table->find(Session::load()->get('user_id'));
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
    return password_hash($password, PASSWORD_BCRYPT, ['salt' => self::load()->config->salt]);
  }

  private function autorize($role)
  {
    if ($this->isAuthenticated) {
      $this->isAuthorized = $this->user->hasRole($role);
    }
  }
}