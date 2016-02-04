<?php
namespace KissPHP;

use KissPHP\Application;

class Exception extends \Exception
{
  public function __construct($message, $code = 0)
  {
    parent::__construct($message, $code);
    $this->writeException($message);
  }


  public function __toString()
  {
    return $this->message;
  }

  private function writeException($e)
  {
    $uri = Application::getBaseDir()."/Log/log.txt";
    $date = date("d/m/Y ~ G\:i  : ");
    $message = $date.$e;

    $log = fopen ($uri, "a+");
    fputs($log, $message);
    fputs($log, "\n");
    fclose($log);
  }
}