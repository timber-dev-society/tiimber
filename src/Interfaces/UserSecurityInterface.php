<?php
namespace Tiimber\Interfaces;

interface UserSecurityInterface
{
  /**
   * return the unique user identifier
   *
   * @return Integer|String
   */
  public function getIndentifier();

  /**
   * Return if the user get the researched role
   *
   * @param String $role
   * @return Boolean
   */
  public function hasRole($role);

  /**
   * Return all role of the user
   *
   * @return Array
   */
  public function getRoles();

  /**
   * Return the user username
   *
   * @return String
   */
  public function getUsername();
}