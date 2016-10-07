<?php

namespace Tiimber\Loggers;

use Tiimber\{Memory, Traits\FolderResolverTrait};

class FileLogger
{
  use FolderResolverTrait;

  public function __construct($level = 0)
  {
    Memory::events()->on('log', function (string $level, string $message) {
      $this->log();
    });
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

  private function log($level, $message)
  {
    $filepath = $this->getLogFolder();
    file_put_contents($filepath, $message . PHP_EOL, FILE_APPEND);
  }
}