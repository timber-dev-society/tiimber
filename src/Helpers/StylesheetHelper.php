<?php
namespace Tiimber\Helpers;

use Tiimber\Interfaces\HelperInterface;
use Tiimber\Config;
use Tiimber\Renderer;
use Tiimber\Exception;

class StylesheetHelper implements HelperInterface
{
  private $url;

  private $external = false;

  public function setRenderer(Renderer $renderer)
  {
  }

  public function setArguments(array $args = null)
  {
    $this->url = isset($args['url']) ? $args['url'] : $args[0];

    if (strpos('http', $this->url) || strpos('//', $this->url) == 1) {
      $this->external = true;
    }
  }

  public function render()
  {
    $url = $this->external
              ? $this->url
              : '/resources/stylesheet/' . $this->url;

    return '<link rel="stylesheet" src="' . $url . '"  type="text/css">';
  }
}