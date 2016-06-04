<?php
namespace Tiimber;

use Tiimber\Logger;

class Exception extends \Exception
{
  use Tiimber\Logger;

  public function __construct($message, $code = 0)
  {
    parent::__construct($message, $code);
    $this->writeLog($message);
  }

  protected function writeLog($message)
  {
    $date = date("d/m/Y ~ G\:i  : ");
    $message = $date . $message;

    $this->critical($message);
  }
}
