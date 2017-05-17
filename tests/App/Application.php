<?php
namespace Tiimber\Tests\App;

use Tiimber\Traits\{ApplicationTrait as Tiimber, ServerTrait as Server};

class Application
{
  use Tiimber, Server;

  private function prepare()
  {
    $this->setRoot(__DIR__);
    $this->setCacheFolder(__DIR__ . '/cache');
    $this->setHost('localhost');
    $this->setPort(1337);
  }

  public function start()
  {
    $this->prepare();
    $this->chop();
    $this->runHttpServer($this->runApp());
  }
}