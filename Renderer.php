<?php
namespace KissPHP;

class Renderer
{
  public $layout;

  public function __construct($layout)
  {
    $this->layout = $layout;
  }

  public function renderTpl($tpl, Array $arguments = null)
  {
    if (is_array($arguments) && !empty($arguments)) {
      extract($arguments);
    }
    $render = $this->getRenderFunction();
    $filename = 'Templates' . DIRECTORY_SEPARATOR . $tpl . '.phtml';

    ob_start();
    include $filename;
    return ob_get_clean();
  }

  public function render($tpl, Array $arguments = null)
  {
    $content = $this->renderTpl($tpl, $arguments);

    $render = $this->getRenderFunction();
    ob_start();
    include 'Templates' . DIRECTORY_SEPARATOR . 'Layouts' . DIRECTORY_SEPARATOR . $this->layout . '.phtml';
    return ob_get_clean();
  }

  private function getRenderFunction()
  {
    return function ($tpl, Array $arguments = null) {
      return call_user_func_array([$this, 'renderTpl'], ['tpl' => $tpl, 'arguments' => $arguments]);
    };
  }
}