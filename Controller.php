<?php
namespace KissPHP;

use KissPHP\Security;
use KissPHP\Render;
use KissPHP\Config;

class Controller
{
  protected $request;

  protected $routes;

  protected $renderer;

  protected $controllers;

  public function __construct(\stdClass $routes)
  {
    $this->request = new Request();
    $this->routes = (array)$routes;
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

    return $this->runAction($route->controller, $route->action, $arguments);
  }

  public function runAction($controllerName, $action, $arguments)
  {
    if (!property_exists($this->controllers, $controllerName)) {
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
}