<?php
declare(strict_types=1);
namespace Tiimber\Interfaces;

interface UserSecurityInterface
{
  /**
   * return the unique user identifier
   *
   * @return Integer|String
   */
  public function getIdentifier();

  /**
   * Return if the user get the researched role
   *
   * @param String $role
   * @return Boolean
   */
  public function hasRole(string $role): bool;

  /**
   * Return all role of the user
   *
   * @return Array
   */
  public function getRoles(): array;

  /**
   * Return the user username
   *
   * @return String
   */
  public function getUsername(): string;
}
