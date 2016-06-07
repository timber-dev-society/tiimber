<?php
namespace Tiimber\Tests\Traits;

use Tiimber\Application;
use Tiimber\Config;
use Tiimber\Traits\RouteResolverTrait;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use PHPUnit_Framework_TestCase;

use stdClass;

class RouteResolverTraitTest extends PHPUnit_Framework_TestCase
{
  private $mock;
  private $routes;

  public function __construct()
  {
    $application = new Application();
    $application->setConfigDir(dirname(__DIR__) . '/Config/');
    $this->mock = $this->getMockForTrait('Tiimber\Traits\RouteResolverTrait');
    $this->routes = Config::get('routes');
  }

  public function testResolving()
  {
    $match = $this->mock->resolve($this->routes, 'GET', '/');
    $this->assertEquals('basic_route', $match['_route']);
    $match = $this->mock->resolve($this->routes, 'GET', '/foo');
    $this->assertEquals('route_with_protocol', $match['_route']);
  }

  public function testResolvingWithParameters()
  {
    $match = $this->mock->resolve($this->routes, 'GET', '/1');
    $this->assertEquals('route_with_param', $match['_route']);
    $this->assertEquals('1', $match['id']);
  }

  public function testResolvingNotFound()
  {
    $this->expectException(ResourceNotFoundException::class);
    $match = $this->mock->resolve($this->routes, 'GET', '/this/is/a/404');
  }
}
