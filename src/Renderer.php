<?php
namespace Tiimber;

use Rb\Redux\Store;

use Tiimber\{Memory, View, Renderer\Engine, Renderer\Includer};
use const Tiimber\Consts\Actions\RENDER;
use function Tiimber\Renderer\Parser\{convertParams, generateTpl};

class Renderer
{
  private $outlets = [];

  private $dispatch;
  
  public function __construct()
  {
    include __DIR__ . DIRECTORY_SEPARATOR . 'Reducers.php';
    $this->store = Store::create($render, []);
  }

  public function parseChunk(string $tpl): array
  {
    $regex = '/\<([A-Z]\w+)\s((?:\w+=(?:\"|\{)?.+(?:\"|\})?\s)*)?\/\>/U';

    $matches = [];
    preg_match_all($regex, $tpl, $matches);
    return $matches;
  }

  protected function convertChunks($view)
  {
    $tpl = $view->render();
    $matches = $this->parseChunk($tpl);

    return generateTpl(0, $matches, $view, $tpl, function ($action, $outlets) {
      var_dump($action);
      $this->store->dispatch(array_merge(
        $action,
        [
          'type' => RENDER,
          'render' => $this,
          'props' => $action->getData(),
          'outlet' => $outlet,
        ]
      ));
    });
  }

  public function renderChunk(View $chunk, array $props)
  {
    $tpl = $this->convertChunks($chunk);

    return Engine::get()->render(
      $chunk->render()
    );
  }

  public function renderExtended(View $view)
  {
    $namespace = $view::EXTEND;
    $extend = new $namespace();
    $tpl = $this->convertChunks($extend);

    $outlets = $this->store->getState();
    $outlets['children'] = $this->renderChunk($view, $view->render([]));

    return Engine::get()->render(
      $tpl,
      $outlets
    );
  }
  
  public function render(View $page): string
  {
    if ($page::EXTEND !== null) {
      return $this->renderExtended($page);
    }
    return $this->renderChunk($page, [[], []]);
  }
}