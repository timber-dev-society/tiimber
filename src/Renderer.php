<?php

namespace Tiimber;

use Tiimber\Memory;

use Tiimber\Renderer\Engine;

class Renderer
{
  private $outlets = [];
  
  public function __construct()
  {
    Memory::events()->on('stop::rendering', function () {
      $this->outlets = [];
    });
  }

  public function outlet($name, $view)
  {
    $this->outlets[$name] = Engine::get()->render(
      $view::TPL, 
      array_merge($view->render(), $this->outlets)
    );
  }
  
  public function render($layout)
  {
    return Engine::get()->render(
      $layout::TPL, 
      $this->outlets
    );
  }
}