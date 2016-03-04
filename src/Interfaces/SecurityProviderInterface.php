<?php
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
  public function loadUserByUsernamePassword($username, $password);

  /**
   * Find the user with this identifier
   *
   * @param $id String|Interger
   * @return UserSecurityInterface
   */
  public function loadUserByIdentifier($id);

  /**
   * Save the user
   *
   * @param UserSecurityInterface $user
   */
  public function saveUser(UserSecurityInterface $user);
}