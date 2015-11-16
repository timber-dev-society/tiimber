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
    $render = function ($tpl, Array $arguments = null) {
      return call_user_func_array([$this, 'renderTpl'], ['tpl' => $tpl, 'arguments' => $arguments]);
    };
    $filename = 'Templates' . DIRECTORY_SEPARATOR . $tpl . '.phtml';

    include $filename;
  }

  public function render($tpl, Array $arguments = null)
  {
    if (is_array($arguments) && !empty($arguments)) {
      extract($arguments);
    }
    $filename = 'Templates' . DIRECTORY_SEPARATOR . $tpl . '.phtml';

    $render = function ($tpl, Array $arguments = null) {
      return call_user_func_array([$this, 'renderTpl'], ['tpl' => $tpl, 'arguments' => $arguments]);
    };

    ob_start();
    include $filename;
    $content = ob_get_clean();

    include 'Templates' . DIRECTORY_SEPARATOR . 'Layouts' . DIRECTORY_SEPARATOR . $this->layout . '.phtml';
  }
}