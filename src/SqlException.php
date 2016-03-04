<?php
namespace Tiimber;

class SqlException extends Exception
{
  public function __construct($message, $sql, $code = 500)
  {
    parent::__construct($message, $code);
    $this->writeLog($sql);
  }
}