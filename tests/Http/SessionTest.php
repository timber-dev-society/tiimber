<?php
namespace Tiimber\Tests\Http;

use Tiimber\Http\Session;

use PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    $conn = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
    $this->session = new Session('12');
  }

  public function testSet()
  {
    $this->session->set('new', 'baz');
    $this->assertEquals('baz', $this->session->get('new'));
    $this->session->set('foo', 'foo');
    $this->assertEquals('foo', $this->session->get('foo'));
  }

  public function testStore()
  {
    $this->session->set('new', 'baz');
    $this->assertEquals('baz', $this->session->get('new'));
    $this->session->store();

    $session = new Session('12');
    $this->assertEquals('baz', $session->get('new'));
    $this->assertTrue($session->get('baz', true));
    $this->assertNull($session->get('baz'));
  }

  public function testUnset()
  {
    $this->session->set('foo', 'foo');
    $this->assertEquals('foo', $this->session->get('foo'));
    $this->session->unset('foo');
    $this->assertNull($this->session->get('foo'));
    $this->session->store();
  }

  public function testDestruct()
  {
    $this->session->set('foo', 'foo');
    $this->assertEquals('foo', $this->session->get('foo'));
    $this->session->destruct();
    $this->assertNull($this->session->get('foo'));
  }
}