<?php

namespace Tiimber\Traits\Server;

use Tiimber\{Config, Dispatcher, Memory, Events\RequestDispatcher, Renderer, Http\Request as HttpRequest};
use const Tiimber\Consts\Scopes\LAYOUT;

trait WebTrait
{
  private $request;
  
  public function runHttpServer(callable $callback)
  {
    $this->request = new RequestDispatcher();
    $this->request->attachEvents();
    $callback(new HttpRequest());
  }
  
  public function runApp(): callable
  {
    return function (HttpRequest $request) {
      $routes = Config::get('routes', []);
      $match = $this->resolve($routes, $request->method, $request->url);
      //$response = (new Dispatcher())->dispatch(strtolower('request::' . $match['_route']), $request, $match);
      
      $this->request->dispatch(
        new Renderer(), 
        strtolower('request::' . $match['_route']), 
        [
          'request' => $request, 
          'args' => $match
        ]
      );
      
      $layout = Memory::get(LAYOUT)->get('\\Blog\\Layouts\\DefaultLayout');
      echo $render->render($layout);
    };
  }
  
  public function setHost($host)
  {
    
  }
  
  public function setPort($port)
  {
    
  }
}