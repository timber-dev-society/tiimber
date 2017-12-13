<?php
namespace Tiimber\Reducer;

use const Tiimber\Consts\Actions\{RENDER, CLEAN};

$chunk = function (?array $state, array $action)
{
  ['type' => $type, 'render' => $render, 'view' => $view, 'props' => $props] = $action;

  switch ($type) {
    case RENDER:
      return $render->renderComponent($view, $props);
    default:
      return $state;
  }
};

$render = function ($state = [], $action) use ($chunk)
{
  ['type' => $type, 'outlet' => $outlet] = $action;

  switch ($type) {
    case RENDER:
      return array_merge(
        $state,
        [$outlet => $chunk(null, $action)]
      );
    case CLEAN:
      return [];
    default:
      return $state;
  }
};
