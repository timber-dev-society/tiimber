<?php
namespace Tiimber;

use Rb\Redux\Store;

use Tiimber\{Memory, Renderer\Engine, Renderer\Includer};
use const Tiimber\Consts\Actions\RENDER;
use function Tiimber\Renderer\Parser\{convertParams, generateTpl};

class Renderer
{
  private $outlets = [];

  private $dispatch;
  
  public function __construct()
  {
    include __DIR__ . DIRECTORY_SEPARATOR . 'Reducer'. DIRECTORY_SEPARATOR . 'TiimberReducer.php';
    $this->store = Store::create($render, []);
  }

  public function parseChunk(string $tpl): array
  {
    $regex = '/\<([A-Z]\w+)\s((?:\w+=(?:\"|\{)?.+(?:\"|\})?\s)*)?\/\>/U';

    $matches = [];
    preg_match_all($regex, $tpl, $matches);
    return $matches;
  }

  public function paramsToProps(array $params, array $values): ?array
  {
    if (empty($params[0])) return [];
    $props = [];
    foreach ($params as $param) {
      $args = explode('=', $param);
      $value = json_decode($args[1]);
      $props[$args[0]] = ($value !== null 
        ? $value 
        : ($values[str_replace(['{', '}'], '', $args[1])] ?? null)
      );
    }
    return $props;
  }

  protected function convertChunks($view)
  {
    $matches = $this->parseChunk($view::TPL);

    // $replace = [];
    // $outlets = [];
    // foreach ($matches[1] as $key => $match) {
    //   $name = 'tiimber-' . $match . '-' . uniqid();
    //   $replace[] = '{{{' . $name . '}}}';
    //   $action = $view->{$match}();
    //   $params = explode(' ', trim($matches[2][$key]));
    //   $this->store->dispatch(array_merge(
    //     $action,
    //     [
    //       'type' => RENDER,
    //       'outlet' => $name,
    //       'render' => $this,
    //       'props' => convertParams(0, explode(' ', trim($matches[2][$key])), $view->render([]), []),
    //     ]
    //   ));
    // }
    // return str_replace($matches[0], $replace, $view::TPL);
    return generateTpl(0, $matches, $view, $view::TPL, function ($action, $outlet, $props) {
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

  public function renderChunk($chunk, array $props)
  {
    $tpl = $this->convertChunks($chunk);

    return Engine::get()->render(
      $tpl,
      $chunk->render($chunk->propsToState([], $props))
    );
  }

  public function renderPage($page)
  {
    $namespace = $page::EXTEND;
    $extend = new $namespace();
    $tpl = $this->convertChunks($extend);

    $outlets = $this->store->getState();
    $outlets['content'] = $this->renderChunk($page, $page->render([]));

    return Engine::get()->render(
      $tpl,
      $outlets
    );
  }
  
  public function render($page)
  {
    return Engine::get()->render(
      $page::TPL,
      $page->render($page->stateToProps([], []))
    );
  }
}