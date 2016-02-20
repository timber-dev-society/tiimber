<?php
namespace KissPHP;

class Request
{
  public $post;

  public $get;

  public $url;

  public $method;

  public $cookie;

  public $session;

  public $referer = null;

  public function __construct()
  {
    $this->post = new ParameterBag($_POST);
    $this->get = new ParameterBag($_GET);
    if ($_SERVER['QUERY_STRING']) {
      $pos = strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);
      $this->url  = substr($_SERVER['REQUEST_URI'], 0, $pos - 1);
    } else {
      $this->url = $_SERVER['QUERY_STRING'];
    }
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->cookie = new ParameterBag($_COOKIE);
    if (isset($_SERVER['HTTP_REFERER'])) {
      $this->referer = $_SERVER['HTTP_REFERER'];
    }
  }
}