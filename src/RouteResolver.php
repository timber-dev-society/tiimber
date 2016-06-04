<?php

namespace Tiimber;

use Tiimber\Request;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RouteResolver
{
  private $route;

  public function __construct(ParameterBag $routes, Request $request)
  {
    $this->routes = $routes;
    $routesCollection = $this->generateRouteCollection();

    $context = new RequestContext('/', $this->request->method);
    $match = (new UrlMatcher($routesCollection, $context))->match($this->request->url);
    $request->definition = $this->routes->get($match['_route']);
    $request->matches = $match;
  }

  private function generateRouteCollection()
  {
    $routes = new RouteCollection();

    foreach ($this->routes as $key => $route) {
      $pattern = explode('::', $route->route, 2);
      $sfRoute = new Route(
        isset($pattern[1]) ? $pattern[1] : $pattern[0]
      );
      if (isset($route->require)) {
        $sfRoute->setRequirements((array)$route->require);
      }
      if (isset($pattern[1])) {
        $sfRoute->setMethods(strtoupper($pattern[0]));
      }
      $routes->add($key, $sfRoute);
    }

    return $routes;
  }
}
