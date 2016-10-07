<?php

namespace Tiimber\Loggers;

use Tiimber\Memory;

class SysLogger
{
  /**
   * A basic usage of error_log
   * See http://php.net/manual/en/function.error-log.php to configure it;
   **/
  public function __construct($level = 0, int $message_type = 0, string $destination = null, string $extra_headers = null)
  {
    Memory::events()->on('log', function (string $level, string $message) use ($message_type, $destination, $extra_headers) {
      error_log($message, $message_type, $destination, $extra_headers);
    });
  }
}