<?php

namespace Tiimber\Traits;

use Tiimber\Bags\ImmutableBag;

use Symfony\Component\Routing\{Route, RouteCollection, RequestContext, Matcher\UrlMatcher};

trait RouteResolverTrait
{

  public function resolve(ImmutableBag $routes, $method, $url)
  {
    $routesCollection = $this->generateRouteCollection($routes);
    $context = new RequestContext('/', $method);

    return (new UrlMatcher($routesCollection, $context))->match($url);
  }

  private function generateRouteCollection(ImmutableBag $routes): RouteCollection
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
