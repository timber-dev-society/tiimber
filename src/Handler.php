<?php
namespace Tiimber;

use Tiimber\Security;
use Tiimber\Render;
use Tiimber\Config;
use Tiimber\ParameterBag;
use Tiimber\Traits\RedirectTrait;

class Handler
{
  use RedirectTrait;

  protected $request;

  protected $renderer;

  protected $controllers;

  public function __construct(Request $request)
  {
    $this->request = $request;

    if (property_exists($request->definition, 'security')) {
      $securityRule = Config::get('security')->get('security')->{$request->definition->security};
      $security =  Security::load()->setSecurityDefinition($securityRule);

      if (!$security->isAuthenticated || !$security->isAuthorized) {
        return $this->redirect($securityRule->redirect);
      }
    }
    $this->renderer = new Renderer(property_exists($route, 'layout') ? $route->layout : 'default');

    $this->controllers = Config::get('controllers');

    echo $this->handle($request->definition->controller, $request->definition->action, $request->matches);
  }

  public function handle($controllerName, $action, $arguments)
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
}
