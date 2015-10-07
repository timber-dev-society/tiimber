<?php
namespace KissPHP;

use KissPHP\Security;
use KissPHP\Render;

class Controller
{
  protected $request;

  protected $routes;

  protected $renderer;

  public function __construct(Array $routes)
  {
    $this->request = new Request();
    $this->routes = $routes;
    $arguments = [];
    $route = $this->getRoute($arguments);

    if (property_exists($route, 'security')) {
      $security =  Security::load()->setSecurityDefinition((object)$route->security, $this->request);
      if (!$security->isAuthenticated || !$security->isAuthorized) {
        header('Location: ' . $route->security['redirect']);
      }
    }
    $this->renderer = new Renderer(property_exists($route, 'layout') ? $route->layout : 'default');

    return $this->runAction($route->controller, $route->action, $arguments);
  }

  public function runAction($controllerName, $action, $arguments)
  {
    $controller = 'KissPHP\\Controllers\\' . $controllerName;
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
    foreach ($this->routes as $pattern => $route) {
      $pattern = explode('::', $pattern, 2);

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