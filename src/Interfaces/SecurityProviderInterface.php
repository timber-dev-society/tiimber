<?php
declare(strict_types=1);
namespace Tiimber\Interfaces;

use Tiimber\Interfaces\UserSecurityInterface;

interface SecurityProviderInterface
{
  /**
   * Find the user with this username and password
   *
   * @param $username String
   * @param $password String
   * @return UserSecurityInterface
   */
  public function loadUser(array $parameters): UserSecurityInterface;

  /**
   * Find the user with this identifier
   *
   * @param $id String|Interger
   * @return UserSecurityInterface
   */
  public function loadUserByIdentifier($id): UserSecurityInterface;

  /**
   * Save the user
   *
   * @param UserSecurityInterface $user
   */
  public function saveUser(UserSecurityInterface $user);
}
