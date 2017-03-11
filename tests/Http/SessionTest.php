<?php
namespace Tiimber\Tests\Http;

use Tiimber\Http\{Cookie, Session};
use React\Http\{Request, Response};
use RingCentral\Psr7\Request as Psr;

use PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
  public $cookie;

  public function __construct()
  {
    $conn = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();

    $this->cookie = new Cookie(
      new Request('GET', '/', [], '1.1', ['Cookie' => 'tiimberid=12']), 
      new Response($conn)
    );
    $this->session = new Session($this->cookie);
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

    $session = new Session($this->cookie);
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