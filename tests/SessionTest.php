<?php
namespace KissPHP\Tests;

use KissPHP\Session;

use PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    Session::load()->set('foo', 'bar');
  }

  public function testSet()
  {
    Session::load()->set('new', 'baz');
    $this->assertEquals('baz', Session::load()->get('new'));
    Session::load()->set('foo', 'foo');
    $this->assertEquals('foo', Session::load()->get('foo'));
  }

  public function testGet()
  {
    $this->assertEquals('bar', Session::load()->get('foo'));
    $this->assertTrue(Session::load()->get('baz', true));
    $this->assertNull(Session::load()->get('baz'));
  }

  public function testDestruct()
  {
    Session::load()->destruct('foo');
    $this->assertNull(Session::load()->get('foo'));
  }
}