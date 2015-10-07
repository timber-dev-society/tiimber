<?php
namespace KissPHP;

use KissPHP\Tables\Users as UserTable;
use KissPHP\Models\User as User;

class Security
{
  public $isAuthenticated = false;

  public $isAuthorized = false;

  private $user;

  private static $instance;

  private function __construct($security = null)
  {
    session_start();
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
      $_SESSION['user_id'] = $user->id;
      return true;
    }

    return false;
  }

  public function getUser()
  {
    return $this->user;
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
    if ($_SESSION['user_id']) {
      unset($_SESSION['user_id']);
    }
  }

  private function refresh()
  {
    if (isset($_SESSION['user_id'])) {
      $table = new UserTable();
      $this->user = $table->find($_SESSION['user_id']);
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