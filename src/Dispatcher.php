<?php
namespace Tiimber;

use Tiimber\Memory;
use Tiimber\Traits\FolderResolverTrait;

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

  public function resolveEvent($event, $parameters)
  {
    foreach (Memory::get('views') as $namespace => $view) {
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
    $m = new Mustache_Engine([
      'cache' => $this->getCacheDir(),
      'pragmas'=>[Mustache_Engine::PRAGMA_FILTERS],
      'helpers'=>[
        'LambdaGet'=>function($lambda){
          return call_user_func($lambda);
        }
      ]
    ]);
    foreach ($this->resolveEvent($event, $parameters) as $namespace => $view) {
      $outlets[$view::EVENTS[$event]] = '<tiimber-fragment view="' . $namespace . '">' . $m->render($view::TPL, $view->render()) . '</tiimber-fragment>';
    }

    $layout = Memory::get('layouts')->get('\\Blog\\Layouts\\DefaultLayout');
    echo $m->render($layout::TPL, $outlets);
  }
}
