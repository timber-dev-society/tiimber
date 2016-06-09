<?php
namespace Tiimber;

use Tiimber\Memory;
use Mustache_Engine;

class Dispatcher
{
  private $request;

  private $eventAction;

  public function getEventAction($request)
  {
    if (!$this->eventAction) {
      $this->eventAction = 'on' . ucfirst(strtolower($request->method));
    }
    return $this->eventAction;
  }

  public function dispatch($event, ...$parameters)
  {
    $outlets = [];
    $m = new Mustache_Engine();
    foreach (Memory::get('views') as $view) {
      if (isset($view::EVENTS[$event])) {
        if (method_exists($view, $this->getEventAction(...$parameters))) {
          $view->{$this->getEventAction(...$parameters)}(...$parameters);
        }
        $outlets[$view::EVENTS[$event]] = $m->render($view::TPL, $view->render());
      }
    }

    $layout = Memory::get('layouts')->get('\\Blog\\Layouts\\DefaultLayout');
    echo $m->render($layout::TPL, $outlets);
  }
}
