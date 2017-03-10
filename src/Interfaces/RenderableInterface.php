<?php

namespace Tiimber\Interfaces;

use Tiimber\Http\Request;

interface RenderableInterface
{
  public function render(): array;
}