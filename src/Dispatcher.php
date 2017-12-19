<?php

namespace Tiimber;

use Tiimber\{Memory, Event};

use const Tiimber\Consts\Scopes\{ACTION, VIEW};
use const Tiimber\Consts\Events\{ERROR, RENDER, REQUEST, ES};

class Dispatcher
{
  private $renderer;

  private $event = [];

  private function loadEvents(string $scope)
  {
    $this->events[$scope] = new Event($scope);
    $this->events[$scope]->attachEvents();
    Memory::events()->on($scope, function ($event, $renderer, $parameters) use ($scope) {
      $this->events[$scope]->dispatch(
        $renderer,
        $this,
        $scope . ES . $event,
        $parameters
      );
    });
  }

  public function emit($event, $name, $render, $args)
  {
    Memory::events()->emit($event, [
      'event' => $name,
      'renderer' => $render,
      'parameters' => $args
    ]);
  }

  public function __construct()
  {
    $this->loadEvents(ERROR);
    $this->loadEvents(RENDER);
    $this->loadEvents(REQUEST);
  }
}
