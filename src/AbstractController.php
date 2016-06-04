<?php
namespace Tiimber;

use Tiimber\Controller;

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

  public function beforeAction($action)
  {
  }

  public function redirect($location, array $args = [])
  {
    Controller::redirect($location, $args);
  }
}
