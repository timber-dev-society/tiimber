<?php
namespace Tiimber\Tests\Mocks;

use Tiimber\Interfaces\SecurityProviderInterface;
use Tiimber\Interfaces\UserSecurityInterface;
use Tiimber\Tests\Mocks\UserSecurityMock;

class SecurityProviderMock implements SecurityProviderInterface;
{
  private $users = [];

  public function loadUser(array $parameters): UserSecurityInterface
  {
    return new UserSecurityMock($parameters->identifier, $parameters->roles);
  }

  public function loadUserByIdentifier($id): UserSecurityInterface
  {
    return $this->users[$id];
  }

  public function saveUser(UserSecurityInterface $user);
  {
    $this->users[$user->getIdentifier] = $user;
  }
}
