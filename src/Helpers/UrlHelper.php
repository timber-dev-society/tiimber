<?php
namespace Tiimber\Helpers;

use Tiimber\Interfaces\HelperInterface;
use Tiimber\Config;
use Tiimber\Renderer;
use Tiimber\Exception;

class UrlHelper implements HelperInterface
{
  public function setRenderer(Renderer $renderer)
  {
  }

  public function setArguments(array $args = null)
  {
    $this->key = isset($args['url']) ? $args['url'] : $args[0];
    if (isset($args['url'])) {
      array_unshift($args, $args['url']);
      unset($args['url']);
    }
    $args[0] = '';
    $this->args = $args;
  }

  private function hydrateRoute($route)
  {
    if (count($this->args) == 1) {
      return $route;
    }

    $elements = preg_split('/\{.+\}/i', $route);
    if (count($elements) == count($this->args)) {
      foreach ($elements as $key => $value) {
        $elements[$key] = (string)$this->args[$key] . $value;
      }
      return implode('', $elements);
    } else {
      throw new Exception('UrlHeper: wrong params number for route ' . $this->key);
    }
  }

  public function render()
  {
    $routes = Config::get('routes');
    $route = $routes->get($this->key, (object)['route' => ''])->route;
    $route = explode('::', $route);
    $route = end($route);

    return $this->hydrateRoute($route);
  }
}