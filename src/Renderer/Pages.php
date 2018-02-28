<?php

namespace Tiimber\Renderer;

use Tiimber\{Memory, Traits\LoggerTrait};

use const Tiimber\Consts\Scopes\PAGE;
use const Tiimber\Consts\Events\{REQUEST, ES};

class Pages
{
  use LoggerTrait;

  private $pages = [];

  public function __construct($routes)
  {
    foreach ($routes as $route => $values) {
      $route = REQUEST . ES . $route;
      $this->pages[$route] = $this->mapPageForRoutes($route);
    }
  }

  public function resolve($route)
  {
    return $this->pages[$route];
  }

  public function resolveErrorLayout($error)
  {
    return $this->mapPageForRoutes($error);
  }

  private function mapPageForRoutes($route)
  {
    $pieces = explode(ES, $route);
    $pages = [];
    $default;

    foreach (Memory::get(PAGE) as $namespace => $page) {
      if (!defined($namespace . '::EVENTS')) {
        Memory::get(PAGE)->unset($namespace);
        $this->warning('No events found for Page '.$namespace);
        continue;
      }
      foreach ($page::EVENTS as $event) {
        $exploded = explode(ES, $event);
        $common = array_intersect($pieces, $exploded);
        $cleaned = array_filter($exploded, function ($value) {
          return $value !== '*';
        });

        if (count($common) > 0 && $common === $cleaned) {
          $pages[count($common)] = $namespace;
        }
      }
    }
    if (count($pages) !== 0) {
      ksort($pages);
      return end($pages);
    } else {
      $this->warning('The route '.$route.' doesn\'t match any pages');
    }
  }
}
