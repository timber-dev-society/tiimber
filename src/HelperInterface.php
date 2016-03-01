<?php
namespace KissPHP;

use KissPHP\Renderer;

interface HelperInterface
{
  public function setRenderer(Renderer $renderer);

  public function setArguments(array $arguments = null);

  public function render();
}