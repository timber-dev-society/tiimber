<?php
namespace Tiimber\Traits;

use Psr\Log\LoggerTrait as PsrLoggerTrait;

use Tiimber\Memory;

use const Tiimber\Consts\Events\LOG;

trait LoggerTrait
{
  use PsrLoggerTrait;

  public function log(string $level, string $message, array $context = [])
  {
    $time = date('d/m/Y ~ G\:i ');
    Memory::events()->emit(LOG, [
      'level' => $level,
      'message' => $time . '[' . $level . '] ' . $message,
      'context' => $context
    ]);
  }
}
