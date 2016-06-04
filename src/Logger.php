<?php
namespace Tiimber;

use Psr\Log\LoggerTrait;

use Tiimber\Application;
use Tiimber\Config;
use Tiimber\ParameterBag;

trait Logger
{
  use LoggerTrait;

  public function log($level, $message, array $context = array())
  {
    $logger = Config::get('drivers', new ParameterBag([]))->get('logger', false);
    if ($logger) {
      $logger->log($level, $message,  $context);
    } else {
      $this->defaultLog($level, $message, $context);
    }
  }

  private function initFolder()
  {
    if (!is_dir(Application::getBaseDir() . '/log')) {
      mkdir(Application::getBaseDir() . '/log');
    }
    if (!file_exists(Application::getBaseDir() . '/log/log.txt')) {
      touch(Application::getBaseDir() . '/log/log.txt');
    }
  }

  private function defaultLog($level, $message, array $context = array())
  {
    $this->initFolder();
    $date = date('d/m/Y ~ G\:i ');
    $filepath = Application::getBaseDir() . '/log/log.txt';
    file_put_contents($filepath, $date . '[' . $level . '] ' . $message, FILE_APPEND);
  }
}
