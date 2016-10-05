<?php
namespace Tiimber\Exceptions;

use Tiimber\Traits\LoggerTrait;

class Exception extends \Exception
{
  use LoggerTrait;

  public function __construct($message, $code = 0)
  {
    parent::__construct($message, $code);
    $this->writeLog($message);
  }

  protected function writeLog($message)
  {
    $date = date("d/m/Y ~ G\:i  : ");
    $message = $date . $message . "\n";

    $this->critical($message);
  }
}
