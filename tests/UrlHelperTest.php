<?php
namespace Tiimber\Tests;

use Tiimber\Helpers\UrlHelper;
use Tiimber\Renderer;

use PHPUnit_Framework_TestCase;

class UrlHelperTest extends PHPUnit_Framework_TestCase
{
  private $helper;

  public function __construct()
  {
    $this->helper = new UrlHelper();
  }

  public function testInit()
  {
    $helper = new UrlHelper();

    $this->assertInstanceOf('Tiimber\\Interfaces\\HelperInterface', $helper);
  }
  
  public function testBasicRoute()
  {
    $this->helper->setArguments(['basic_route']);

    $this->assertEquals('/', $this->helper->render());
  }

  public function testRouteWithParams()
  {
    $this->helper->setArguments(['route_with_param', 2]);

    $this->assertEquals('/2', $this->helper->render());
  }

  public function testRouteWithProtocol()
  {
    $this->helper->setArguments(['route_with_protocol']);

    $this->assertEquals('/foo', $this->helper->render());
  }
}