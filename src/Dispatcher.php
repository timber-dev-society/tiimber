<?php
namespace Tiimber;

use Tiimber\Memory;
use Tiimber\Traits\FolderResolverTrait;

use const Tiimber\Memory\Scopes\{LAYOUT, HELPER, VIEW, ACTION};

use Mustache_Engine;

class Dispatcher
{
  use FolderResolverTrait;

  private $request;

  private $eventAction;

  private function getEventAction($request)
  {
    if (!$this->eventAction) {
      $this->eventAction = 'on' . ucfirst(strtolower($request->method));
    }
    return $this->eventAction;
  }
  
  private function getWildCards($event)
  {
    $wildcards = [$event];
    $pieces = explode('::', $event);
    
    for ($length = count($pieces); $length >= 2; --$length) {
      $key = $length - 1;
      $pieces[$key] = '*';
      $wildcards[] = implode('::', $pieces);
      unset($pieces[$key]);
    }
    
    return $wildcards;
  }

  public function dispatchActionEvent($events, $parameters)
  {
    foreach (Memory::get(ACTION) as $namespace => $action) {
      if (count(array_intersect($events, $action::EVENTS)) !== 0) {
        if (method_exists($action, $this->getEventAction(...$parameters))) {
          $action->{$this->getEventAction(...$parameters)}(...$parameters);
        }
      }
    }
  }

  public function resolveViewEvent($events, $parameters)
  {
    foreach (Memory::get(VIEW) as $namespace => $view) {
      $intersect = array_intersect($events, array_keys($view::EVENTS));
      if (count($intersect) !== 0) {
        $event = reset($intersect);
        if (method_exists($view, $this->getEventAction(...$parameters))) {
          $view->{$this->getEventAction(...$parameters)}(...$parameters);
        }
        yield $view::EVENTS[$event] => $view;
      }
    }
  }
  
  private function getHelper()
  {
    $helpers = [];
    foreach (Memory::get(HELPER) as $namespace => $helper) {
      $pieces = explode('\\', $namespace);
      $classname = end($pieces);
      $helpers[strtolower($classname)] = function ($text) use ($helper) {
        return $helper->render($text);
      };
    }
    return $helpers;
  }

  public function dispatch($event, ...$parameters)
  {
    $outlets = [];
    
    $m = new Mustache_Engine([
      'cache' => $this->getCacheDir(),
      'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
      'helpers' => $this->getHelper()
    ]);
    $events = $this->getWildCards($event);
    $this->dispatchActionEvent($events, $parameters);

    foreach ($this->resolveViewEvent($events, $parameters) as $outlet => $view) {
      $outlets[$outlet] = $m->render($view::TPL, $view->render());
    }

    $layout = Memory::get(LAYOUT)->get('\\Blog\\Layouts\\DefaultLayout');
    echo $m->render($layout::TPL, $outlets);
  }
}
