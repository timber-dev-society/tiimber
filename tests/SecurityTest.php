<?php
namespace Tiimber\Tests;

use Tiimber\Security;
use Tiimber\Tests\Mocks\UserSecurityMock;

use PHPUnit_Framework_TestCase;

class SecurityTest extends PHPUnit_Framework_TestCase
{
  public function testAuthenticate()
  {
    $user = new UserSecurityMock('foo', ['bar']);
    $return = Security::load()->authenticate([identifier => 'foo', roles => ['bar']]);
    $this->assertTrue($return);
    $this->assertEquals($user, Security::load()->getUser());
  }
}
