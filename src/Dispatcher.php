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

  public function getEventAction($request)
  {
    if (!$this->eventAction) {
      $this->eventAction = 'on' . ucfirst(strtolower($request->method));
    }
    return $this->eventAction;
  }

  public function dispatchActionEvent($event, $parameters)
  {
    foreach (Memory::get(ACTION) as $namespace => $action) {
      if (in_array($event, $action::EVENTS)) {
        if (method_exists($action, $this->getEventAction(...$parameters))) {
          $action->{$this->getEventAction(...$parameters)}(...$parameters);
        }
      }
    }
  }

  public function resolveViewEvent($event, $parameters)
  {
    foreach (Memory::get(VIEW) as $namespace => $view) {
      if (isset($view::EVENTS[$event])) {
        if (method_exists($view, $this->getEventAction(...$parameters))) {
          $view->{$this->getEventAction(...$parameters)}(...$parameters);
        }
        yield $namespace => $view;
      }
    }
  }

  public function dispatch($event, ...$parameters)
  {
    $outlets = [];
    $helpers = [];
    foreach (Memory::get(HELPER) as $namespace => $helper) {
      $pieces = explode('\\', $namespace);
      $classname = end($pieces);
      $helpers[strtolower($classname)] = function ($text) use ($helper) {
        return $helper->render($text);
      };
    }
    $m = new Mustache_Engine([
      'cache' => $this->getCacheDir(),
      'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
      'helpers' => $helpers
    ]);

    $this->dispatchActionEvent($event, $parameters);

    foreach ($this->resolveViewEvent($event, $parameters) as $namespace => $view) {
      $outlets[$view::EVENTS[$event]] = '<tiimber-fragment view="' . $namespace . '">' . $m->render($view::TPL, $view->render()) . '</tiimber-fragment>';
    }

    $layout = Memory::get(LAYOUT)->get('\\Blog\\Layouts\\DefaultLayout');
    echo $m->render($layout::TPL, $outlets);
  }
}
