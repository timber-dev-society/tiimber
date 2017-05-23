<?php
namespace Tiimber\Reducer;

use Tiimber\{Reducer\CollectionReducer, Renderer};
use const Tiimber\Consts\Action\RENDER;

class TiimberReducer extends CollectionReducer
{
  public $defaultState = [];

  public function onAction($state, $action)
  {
    switch ($action['type']) {
      case RENDER:
        return array_merge(
          $state,
          [$action['outlet'] => $action['render']->renderChunk($action['tpl'])]
        );
      default:
        return state;
    }
  }
}
