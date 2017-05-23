<?php

namespace Tiimber;

use Tiimber\Memory;

use Tiimber\Renderer\{Engine, Includer};

class Renderer
{
  private $outlets = [];

  private $dispatch;
  
  public function __construct($store)
  {
    Memory::events()->on('stop::rendering', function () {
      $this->outlets = [];
    });
    $this->store = $store;
  }

  public function outlet($name, $view)
  {
    $this->outlets[$name] = Engine::get()->render(
      $view::TPL, 
      array_merge($view->render(), $this->outlets)
    );
  }

  public function renderPage($page)
  {
    $namespace = $page::EXTEND;
    $extend = new $namespace();
    $tpl = $this->convertChuncks($extend);

    $outlets = $store->getState();
    $outlets['content'] = $this->renderChunck($page);
    return Engine::get()->render(
      $tpl,
      $outlets
    );
  }

  protected function convertChuncks($view)
  {
    $matches = (new Includer())->parse($view::TPL);
    if (count($matches) === 0) { return $view::TPL; }

    $replace = [];
    $outlets = [];
    foreach ($matches[1] as $match) {
      $name = 'tiimber-' . $match . '-' . uniqid();
      $replace[] = '{{{' . $name . '}}}';
      $action = $extend->{$match}();
      $this->store->dispatch(array_merge(
        $action,
        [
          'outlet' => $name,
          'render' => $this,
        ]
      ));
    }
    return str_replace($matches[0], $replace, $extend::TPL);
  }

  public function renderChunck($chunck)
  {
    $tpl = $this->convertChuncks($chunck);

    return Engine::get()->render(
      $tpl,
      $chunck->render([])
    );
  }
  
  public function render($page)
  {
    return Engine::get()->render(
      $page::TPL,
      $page->render($page->stateToProps([], []))
    );
  }

  public function refresh()
  {
    $this->outlets = [];
  }
}