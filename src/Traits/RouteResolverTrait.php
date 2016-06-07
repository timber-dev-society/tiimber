<?php

namespace Tiimber\Traits;

use Tiimber\ParameterBag;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

trait RouteResolverTrait
{

  public function resolve(ParameterBag $routes, $method, $url)
  {
    $routesCollection = $this->generateRouteCollection($routes);
    $context = new RequestContext('/', $method);

    return (new UrlMatcher($routesCollection, $context))->match($url);
  }

  private function generateRouteCollection(ParameterBag $routes): RouteCollection
  {
    $routeCollection = new RouteCollection();

    foreach ($routes as $key => $route) {
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
      $routeCollection->add($key, $sfRoute);
    }

    return $routeCollection;
  }
}
