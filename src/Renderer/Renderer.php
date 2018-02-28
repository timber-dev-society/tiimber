<?php
namespace Tiimber\Renderer;

use Rb\Redux\Store;

use Tiimber\{Memory, Renderer\Base\View, Renderer\Includer};
use const Tiimber\Consts\Actions\RENDER;
use function Tiimber\Renderer\Parser\{convertParams, generateTpl};

class Renderer
{
  private $outlets = [];

  private $dispatch;

  public function __construct()
  {
    include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Reducers.php';
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

    return generateTpl(0, $matches, $view, $tpl, function ($action, $outlet, $props) {

      $this->store->dispatch(array_merge(
        $action,
        [
          'type' => RENDER,
          'render' => $this,
          'props' => $props,
          'outlet' => $outlet,
        ]
      ));
    });
  }

  public function renderComponent(string $className, array $props)
  {
    $component = (new $className())->initialize($props);
    return $this->renderChunk($component, $props);
  }

  public function renderChunk(View $chunk, array $props)
  {
    $tpl = $this->convertChunks($chunk);

    return $tpl;
  }

  public function renderExtended(View $view, string $children): string
  {
    $namespace = $view::EXTEND;

    $extend = (new $namespace())->initialize(['children' => $children]);
    $tpl = $this->convertChunks($extend);
    $state = $this->store->getState();

    return str_replace(array_keys($state), $state, $tpl);
  }

  public function render(string $namespace, array $props): string
  {
    $page = new $namespace();
    $page->initialize($props);
    $children = $this->convertChunks($page, $page->getData());
    if ($page::EXTEND !== null) {
      return $this->renderExtended($page, $children);
    }
    return $children;
  }
}
