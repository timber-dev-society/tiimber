<?php
namespace Tiimber;

class Renderer
{
  public $layout;

  public function __construct($layout)
  {
    $this->layout = $layout;
    $this->helpers = Config::get('helpers');
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

  public function __call($helper, $arguments = null)
  {
    if (!property_exists($this->helpers, $helper)) {
      throw new Exception($helper . ' isn\'t define into config file');
    }
    $helper = '\\' . $this->helpers->{$helper};
    $helper = new $helper();

    if ($helper instanceof HelperInterface) {
      $helper->setRenderer($this);
      $helper->setArguments($arguments[0]);
      return $helper->render();
    }

    throw new Exception($helper . ' doesn\'t implement HelperInterface');
  }

  private function getRenderFunction()
  {
    return function ($tpl, Array $arguments = null) {
      return call_user_func_array([$this, 'renderTpl'], ['tpl' => $tpl, 'arguments' => $arguments]);
    };
  }
}