<?php
namespace Tiimber\Traits;

use Psr\Log\LoggerTrait as PsrLoggerTrait;

use Tiimber\Memory;

trait LoggerTrait
{
  use PsrLoggerTrait;

  public function log(string $level, string $message, array $context = [])
  {
    $time = date('d/m/Y ~ G\:i ');
    Memory::events()->emit('log', [
      'level' => $level,
      'message' => $time . '[' . $level . '] ' . $message,
      'context' => $context
    ]);
  }
}
