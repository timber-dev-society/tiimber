<?php
namespace KissPHP;

use KissPHP\Tables\Users as UserTable;
use KissPHP\Models\User as User;
use KissPHP\Session as Session;

class Security
{
  public $isAuthenticated = false;

  public $isAuthorized = false;

  private $user;

  private static $instance;

  private function __construct($security = null)
  {
    $this->refresh();
  }

  public static function load()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function setUser(User $user)
  {
    $this->user = $user;
    if ($user) {
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
    $table = new UserTable();

    $user = $table->findOneBy(['username' => $request->post->login, 'password' => $request->post->password]);

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
      $table = new UserTable();
      $this->user = $table->find(Session::load()->get('user_id'));
      $this->isAuthenticated = ($this->user !== null);
    }
  }

  private function autorize($role)
  {
    if ($this->isAuthenticated) {
      $this->isAuthorized = $this->user->hasRole($role);
    }
  }
}