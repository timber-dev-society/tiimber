<?php

namespace Tiimber;

use Evenement\EventEmitterTrait;

use Tiimber\{Action, View, Renderer, Memory, Interfaces\DispatcherInterface, Traits\LoggerTrait};

use const Tiimber\Consts\Scopes\{ACTION, VIEW};
use const Tiimber\Consts\Events\{ERROR, RENDER, REQUEST, ON, STOP, ES, WILDCARD};
use const Tiimber\Consts\LogLevel\{INFO};

class Event
{
  use EventEmitterTrait, LoggerTrait;

  private $renderer;

  private $eventAction;
  
  private $isLocked;
  
  private $scope;
  
  public function __construct($scope)
  {
    $this->scope = $scope;
    Memory::events()->on(ON, function () {
      $this->isLocked = false;
    });
    Memory::events()->on(STOP, function () {
      $this->eventAction = false;
      $this->isLocked = true;
    });
  }

  public function dispatch(Renderer $renderer, $dispatcher, string $event, array $parameters)
  {
    $this->renderer = $renderer;
    $this->dispatcher = $dispatcher;
    foreach ($this->getWildCards($event) as $event) {
      $this->emit($event, $parameters);
    }
  }
  
  public function attachEvents()
  {
    foreach (Memory::get(ACTION) as $namespace => $action) {
      $this->attachActionEvents($action, $namespace);
    }
    foreach (Memory::get(VIEW) as $namespace => $view) {
      $this->attachViewEvents($view, $namespace);
    }
  }
  
  public function accept(string $event): bool
  {
    return strpos($event, $this->scope) === 0;
  }

  private function attachActionEvents(Action $action, $namespace)
  {
    foreach ($action::EVENTS as $event) {
      if (strpos($event, $this->scope) === 0) {
        $this->on($event, function ($request, $args) use ($action, $event, $namespace) {
          $this->log('info', $namespace . ' intersept ' . $event);
          $this->executeAction($action, $request, $args);
          if (method_exists($action, 'call')) {
            $action->call($request, $args);
          }
        });
      }
    }
  }
  
  private function propageRenderEvent($namespace, $request, $args)
  {
    $pieces = explode('\\', $namespace);
    $pieces = array_slice($pieces, 3);
    $event = strtolower(implode(ES, $pieces));

    $this->dispatcher->emit(
      RENDER, 
      str_replace('view', '', $event),
      $this->renderer,
      [
        'requests' => $request,
        'args' => $args
      ]
    );
  }

  private function attachViewEvents(view $view, $namespace)
  {
    foreach ($view::EVENTS as $event => $outlet) {
      if (strpos($event, $this->scope) === 0) {
        $this->on($event, function ($request, $args) use ($view, $outlet, $namespace, $event) {
          if (!$this->isLocked) {
            $this->log(INFO, $namespace . ' intersept ' . $event);
            $this->propageRenderEvent($namespace, $request, $args);
            $this->executeAction($view, $request, $args);
            if (method_exists($view, 'onCall')) {
              $view->onCall($request, $args);
            }
            $this->renderer->outlet($outlet, $view);
          }
        });
      }
    }
  }

  private function getEventAction($request)
  {
    if (!$this->eventAction) {
      $this->eventAction = 'on' . ucfirst(strtolower($request->getMethod()));
    }
    return $this->eventAction;
  }

  private function getWildCards($event)
  {
    $wildcards = [$event];
    $pieces = explode(ES, $event);

    for ($length = count($pieces); $length >= 2; --$length) {
      $key = $length - 1;
      $pieces[$key] = WILDCARD;
      $wildcards[] = implode(ES, $pieces);
      unset($pieces[$key]);
    }

    return $wildcards;
  }

  private function executeAction(Action $action, $request, $args)
  {
    $event = $this->getEventAction($request);
    if (method_exists($action, $event)) {
      $this->log(INFO, $event . ' method called');
      $action->{$event}($request, $args);
    }
  }
}