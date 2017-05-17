<?php

namespace Tiimber\Renderer;

use Tiimber\Memory;

use const Tiimber\Consts\Scopes\LAYOUT;
use const Tiimber\Consts\Events\{REQUEST, ES};

class Layout
{
  private $layouts = [];

  public function __construct($routes)
  {
    foreach ($routes as $route => $values) {
      $route = REQUEST . ES . $route;
      $this->layouts[$route] = $this->findGoodLayout($route);
    }
  }

  public function resolve($route)
  {
    return Memory::get(LAYOUT)->get($this->layouts[$route]);
  }

  public function resolveErrorLayout($error)
  {
    return $this->findGoodLayout($error);
  }

  private function findGoodLayout($route)
  {
    $pieces = explode(ES, $route);
    $layouts = [];
    $default;

    foreach (Memory::get(LAYOUT) as $namespace => $layout) {
      if (strpos('Default', $namespace) !== -1) {
        $default = $namespace;
      }
      if (!defined($namespace . '::EVENTS')) continue;
      foreach ($layout::EVENTS as $event) {
        $common = array_intersect($pieces, explode(ES, $event));
        if (count($common) > 1) {
          $layouts[count($common)] = $namespace;
        }
      }
    }
    if (count($layouts) !== 0) {
      ksort($layouts);
      return end($layouts);
    }
    return $default;
  }
}