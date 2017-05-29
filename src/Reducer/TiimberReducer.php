<?php
namespace Tiimber\Reducer;

use const Tiimber\Consts\Actions\RENDER;

$chunk = function (?array $state, array $action)
{
  switch ($action['type']) {
    case RENDER:
      return $action['render']->renderChunk($action['view'], $action['props']);
    default:
      return $state;
  }
};

$render = function ($state = [], $action) use ($chunk)
{
  switch ($action['type']) {
    case RENDER:
      return array_merge(
        $state,
        [$action['outlet'] => $chunk(null, $action)]
      );
    default:
      return $state;
  }
};
