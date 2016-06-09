<?php
namespace Tiimber;

use Tiimber\Memory;
use Mustache_Engine;

class Dispatcher
{
  private $request;

  public function dispatch($event, ...$parameters)
  {
    $outlets = [];
    $m = new Mustache_Engine();
    foreach (Memory::get('views') as $view) {
      if (array_key_exists($event, $view::EVENTS)) {
        $outlets[$view::EVENTS[$event]] = $m->render($view::TPL, $view->render(...$parameters));
      }
    }

    $layout = Memory::get('layouts')->get('\\Blog\\Layouts\\DefaultLayout');
    echo $m->render($layout::TPL, $outlets);
  }
}
