<?php

namespace Tiimber;

use RuntimeException;

use Evenement\EventEmitterTrait;

use Tiimber\{Action, View, Renderer, Memory, Traits\LoggerTrait};
use Tiimber\Interfaces\{DispatcherInterface, ActionInterface, RenderableInterface};

use const Tiimber\Consts\Scopes\{ACTION, VIEW};
use const Tiimber\Consts\Events\{ERROR, RENDER, REQUEST, ON, STOP, ES, WILDCARD};
use const Tiimber\Consts\LogLevel\{INFO};

use Tiimber\Renderer\Includer;

class Event
{
  use EventEmitterTrait, LoggerTrait;

  private $renderer;
  
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

  public function dispatch(Renderer $renderer, $dispatcher, string $event, array $parameters): Renderer
  {
    $this->renderer = $renderer;
    $this->dispatcher = $dispatcher;
    foreach ($this->getWildCards($event) as $event) {
      $this->emit($event, $parameters);
    }
    return $renderer;
  }

  private function checkActionInterface($action, $namespace)
  {
    if (!$action instanceof ActionInterface) {
      throw new RuntimeException($namespace . ' must implement \\Tiimber\\Interfaces\\ActionInterface');
    }
  }

  private function checkViewInterface($view, $namespace)
  {
    //$this->checkActionInterface($view, $namespace);

    if (!$view instanceof RenderableInterface) {
      throw new RuntimeException($namespace . ' must implement \\Tiimber\\Interfaces\\RenderableInterface');
    }
  }
  
  public function attachEvents()
  {
    /*foreach (Memory::get(ACTION) as $namespace => $action) {
      $this->checkActionInterface($action, $namespace);
      $this->attachActionEvents($action, $namespace);
    }

    /*foreach (Memory::get(VIEW) as $namespace => $view) {
      $this->checkViewInterface($view, $namespace);
      $this->attachViewEvents($view, $namespace);
    }*/
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
          $action->onCall($request, $args);
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

  private function attachViewEvents(View $view, $namespace)
  {
    foreach ($view::EVENTS as $event) {
      if (strpos($event, $this->scope) === 0) {
        $this->on($event, function ($request, $args) use ($view, $namespace, $event) {
          if (!$this->isLocked) {
            $this->log(INFO, $namespace . ' intersept ' . $event);

            $subrend = (new Includer())->parse($view::TPL);
            var_dump($subrend);


            /*$this->propageRenderEvent($namespace, $request, $args);
            $this->executeAction($view, $request, $args);
            $view->onCall($request, $args);
            $this->renderer->outlet($outlet, $view);*/
          }
        });
      }
    }
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
    $action->{'on' . ucfirst(strtolower($request->getMethod()))}($request, $args);
  }
}