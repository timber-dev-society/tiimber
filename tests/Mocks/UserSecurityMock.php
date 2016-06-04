<?php
namespace Tiimber\Tests\Mocks;

use Tiimber\Interfaces\UserSecurityInterface;

class UserSecurityMock implements UserSecurityInterface
{
  private $identifier;

  private $roles;

  public function __construct($identifier, $roles)
  {
    $this->identifier = $identifier;
    $this->roles = $roles;
  }

  public function getIdentifier()
  {
    return $this->identifier;
  }

  public function hasRole(string $role): bool
  {
    return in_array($role, $this->roles);
  }

  public function getRoles(): array
  {
    return $this->roles;
  }

  public function getUsername(): string
  {
    return $this->identifier;
  }
}
