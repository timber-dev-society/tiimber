<?php
namespace Tiimber;

use Tiimber\Security;
use Tiimber\Render;
use Tiimber\Config;
use Tiimber\ParameterBag;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class Controller
{
  protected $request;

  protected $routes;

  protected $renderer;

  protected $controllers;

  public function __construct(ParameterBag $routes)
  {
    $this->request = new Request();
    $this->routes = $routes;
    $routesCollection = $this->generateRouteCollection();

    $context = new RequestContext('/', $this->request->method);
    $match = (new UrlMatcher($routesCollection, $context))->match($this->request->url);
    $arguments = [];
    $route = $this->getRoute($arguments);

    if (property_exists($route, 'security')) {
      $securityRule = Config::get('security')->security->{$route->security};
      $security =  Security::load()->setSecurityDefinition($securityRule);
      if (!$security->isAuthenticated || !$security->isAuthorized) {
        header('Location: ' . $securityRule->redirect);
      }
    }
    $this->renderer = new Renderer(property_exists($route, 'layout') ? $route->layout : 'default');

    $this->controllers = Config::get('controllers');

    echo $this->runAction($route->controller, $route->action, $arguments);
  }

  public function runAction($controllerName, $action, $arguments)
  {
    if (!isset($this->controllers->{$controllerName})) {
      throw new Exception('No class found into config file for controller: ' . $controllerName);
    }
    $controller = '\\' . $this->controllers->{$controllerName};
    $controller = new $controller($this->request, $this->renderer);
    $controller->tpl = $controllerName . DIRECTORY_SEPARATOR . $action;

    $controller->beforeAction($action);
    $method = $action . 'Action';

    if (method_exists($controller, $method)) {
      return call_user_func_array([$controller, $method], $arguments);
    }
    return $controller->render();
  }

  public function getRoute(&$matches)
  {
    foreach ($this->routes as $route) {
      $pattern = explode('::', $route->route, 2);

      if (
        (count($pattern) === 1 && $this->urlMatch($pattern[0], $matches)) ||
        (count($pattern) === 2 && $this->urlMatch($pattern[1], $matches) && $this->methodMatch($pattern[0]))
      ) {
        unset($matches[0]);
        return (object)$route;
      }
    }
  }

  private function urlMatch($pattern, &$matches)
  {
    return preg_match('/^' . addcslashes($pattern, '/') . '$/i', $this->request->url, $matches);
  }

  private function methodMatch($method)
  {
    return strtoupper($method) === $this->request->method;
  }

  private function generateRouteCollection()
  {
    $routes = new RouteCollection();

    foreach ($this->routes as $key => $route) {
      $pattern = explode('::', $route->route, 2);
      $sfRoute = new Route(
        isset($pattern[1]) ? $pattern[1] : $pattern[0]
      );
      if (isset($route->requirement)) {
        $sfRoute->setRequirements($route->requirement);
      }
      if (isset($pattern[1])) {
        $sfRoute->setMethods(strtoupper($pattern[0]));
      }
      $routes->add($key, $sfRoute);
    }

    return $routes;
  }
}