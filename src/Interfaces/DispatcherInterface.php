<?php

namespace Tiimber\Interfaces;

use Tiimber\Renderer;

/**
 * undocumented class
 *
 * @package default
 * @author `g:snips_author`
 */
interface DispatcherInterface
{
  public function attachEvents();
  
  public function accept(string $event): bool;

  public function dispatch(Renderer $renderer, string $event, array $parameters);
}