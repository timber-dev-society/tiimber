<?php
namespace Tiimber\Renderer;

class Includer
{
  public function parse(string $tpl): array
  {
    $regex = '/\<([A-Z]\w+)\s\/\>/';

    $matches = [];
    preg_match_all($regex, $tpl, $matches);
    return $matches;
  }
}