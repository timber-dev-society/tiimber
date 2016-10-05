<?php
namespace Tiimber\Traits;

use Psr\Log\LoggerTrait as PsrLoggerTrait;

use Tiimber\Application;
use Tiimber\Config;
use Tiimber\ParameterBag;
use Tiimber\Traits\FolderResolverTrait;

trait LoggerTrait
{
  use PsrLoggerTrait, FolderResolverTrait;

  public function log($level, $message, array $context = array())
  {
    $logger = Config::get('drivers', new ParameterBag())->get('logger', false);
    if ($logger) {
      $logger->log($level, $message,  $context);
    } else {
      $this->defaultLog($level, $message, $context);
    }
  }

  private function getLogFolder()
  {
    if (!is_dir($this->getBaseDir() . '/log')) {
      mkdir($this->getBaseDir() . '/log');
    }
    if (!file_exists($this->getBaseDir() . '/log/tiimber.log')) {
      touch($this->getBaseDir() . '/log/tiimber.log');
    }

    return $this->getBaseDir() . '/log/tiimber.log';
  }

  private function defaultLog($level, $message, array $context = array())
  {
    $filepath = $this->getLogFolder();
    $date = date('d/m/Y ~ G\:i ');
    file_put_contents($filepath, $date . '[' . $level . '] ' . $message . "\n", FILE_APPEND);
  }
}
