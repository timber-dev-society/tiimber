<?php
namespace Tiimber\Tests\Traits;

use Tiimber\{Memory, Config, Traits\RouteResolverTrait};

use const Tiimber\Consts\{Folder\CONFIG, Scopes\FOLDER};

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use PHPUnit_Framework_TestCase;

use stdClass;

class RouteResolverTraitTest extends PHPUnit_Framework_TestCase
{
  private $mock;
  private $routes;

  public function __construct()
  {
    Memory::set(FOLDER)->set(CONFIG, dirname(__DIR__) . '/Config/');
    $this->mock = $this->getMockForTrait('Tiimber\Traits\RouteResolverTrait');
    $this->routes = Config::get('routes');
  }

  public function testResolving()
  {
    $match = $this->mock->resolve($this->routes, 'GET', '/');
    $this->assertEquals('basic', $match['_route']);

    $match = $this->mock->resolve($this->routes, 'GET', '/foo');
    $this->assertEquals('with::protocol', $match['_route']);
  }

  public function testResolvingWithParameters()
  {
    $match = $this->mock->resolve($this->routes, 'GET', '/1');
    $this->assertEquals('with::param', $match['_route']);
    $this->assertEquals('1', $match['id']);
  }

  public function testResolvingNotFound()
  {
    $this->expectException(ResourceNotFoundException::class);
    $this->mock->resolve($this->routes, 'GET', '/this/is/a/404');
  }
}
