<?php
namespace Tiimber\Tests;

use Tiimber\Application;
use Tiimber\Security;
use Tiimber\Session;
use Tiimber\ParameterBag;

use Tiimber\Tests\Mocks\UserSecurityMock;
use Tiimber\Tests\Mocks\SecurityProviderMock;

use PHPUnit_Framework_TestCase;

class SecurityTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    $application = new Application();
    $application->setConfigDir(__DIR__ . '/Config/');
  }

  public function testAuthenticate()
  {
    $user = new UserSecurityMock('foo', ['bar']);
    $return = Security::load()->authenticate([identifier => 'foo', roles => ['bar']]);
    $this->assertTrue($return);
    $this->assertEquals($user, Security::load()->getUser());
  }

  public function testSetSecurityDefinition()
  {
    Security::load()->setUser((new SecurityProviderMock())->loadUserByIdentifier('foo'));
    Security::load()->setSecurityDefinition((object)['role' => 'bar']);

    $this->assertTrue(Security::load()->isAuthenticated);
    $this->assertTrue(Security::load()->isAuthorized);

    Security::load()->setSecurityDefinition((object)['role' => 'baz']);
    $this->assertTrue(Security::load()->isAuthenticated);
    $this->assertFalse(Security::load()->isAuthorized);
  }

  public function testLogout()
  {
    Security::load()->setUser((new SecurityProviderMock())->loadUserByIdentifier('foo'));
    $this->assertEquals('foo', Session::load()->get(Security::SESSION_ID, false));
    Security::load()->logout();
    $this->assertFalse(Session::load()->get(Security::SESSION_ID, false));
  }

  public function testIsAuthenticated()
  {
    Security::load()->setUser((new SecurityProviderMock())->loadUserByIdentifier('foo'));

    $this->assertTrue(Security::load()->isAuthenticated());
    $this->assertTrue(Security::load()->isAuthenticated('bar'));
    $this->assertFalse(Security::load()->isAuthenticated('baz'));
  }
}
