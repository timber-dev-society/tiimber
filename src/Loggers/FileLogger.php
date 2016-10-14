<?php

namespace Tiimber\Loggers;

use Tiimber\{Memory, Traits\FolderResolverTrait};

use const Tiimber\Consts\LogLevel\DEBUG;
use const Tiimber\Consts\Folder\DS;
use const Tiimber\Consts\Events\LOG;

class FileLogger extends AbstractLogger
{
  use FolderResolverTrait;
  
  private $dest;

  public function __construct($level = DEBUG, $dest = 'tiimber.log')
  {
    $this->dest = $det;
    $this->setBaseLevel($level);
    Memory::events()->on(LOG, function (string $level, string $message) {
      if ($this->isLoggable($level)) {
        $this->log($message);
      }
    });
  }
  
  private function getLogFile()
  {
    $filename = $this->getBaseDir() . DS . 'log' . DS . $this->dest;
    if (!is_dir($this->getBaseDir() . DS .'log')) {
      mkdir($this->getBaseDir() . DS .'log');
    }
    if (!file_exists($filename)) {
      touch($filename);
    }

    return $filename;
  }

  private function log($message)
  {
    $filepath = $this->getLogFile();
    file_put_contents($filepath, $message . PHP_EOL, FILE_APPEND);
  }
}