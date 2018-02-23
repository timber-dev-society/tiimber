<?php
namespace Tiimber\Style;

use Tiimber\Memory;
use Tiimber\Injection\Injected;
use Tiimbet\Style\Interface\StyledView;

class Interceptor extends Injected
{

  public function require(): array
  {
    return [
      Memory::class
    ]
  }

  public function initialize()
  {
    $this->get(Memory::class)->on(RENDER, function ($class) {
      if ($class instanceof StyledView) {
        $this->styles = array_merge($this->styles, $class->style);
      }
    })
  }
}
