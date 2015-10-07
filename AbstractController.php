<?php
namespace KissPHP;

abstract class AbstractController
{
  protected $request;

  protected $renderer;

  public $tpl;

  public function __construct($request, $renderer)
  {
    $this->request = $request;
    $this->renderer = $renderer;
  }

  public function render(Array $arguments = null)
  {
    $this->renderer->render($this->tpl, $arguments);
  }

  public function beforeAction($action)
  {
  }

  public function redirect($location)
  {
    header('Location: ' . $location);
  }
}