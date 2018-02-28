<?php
namespace Tiimber\Loggers;

use Tiimber\Memory;

use const Tiimber\Consts\LogLevel\DEBUG;
use const Tiimber\Consts\Events\LOG;

class ConsoleLogger extends AbstractLogger
{
  /**
   * A basic usage of error_log
   * See http://php.net/manual/en/function.error-log.php to configure it;
   **/
  public function __construct($level = DEBUG, int $message_type = 0, string $destination = null, string $extra_headers = null)
  {
    $this->setBaseLevel($level);
    Memory::events()->on(LOG, function (string $level, string $message) use ($message_type, $destination, $extra_headers) {
      if ($this->isLoggable($level)) {
        error_log($message, $message_type, $destination, $extra_headers);
      }
    });
  }
}
