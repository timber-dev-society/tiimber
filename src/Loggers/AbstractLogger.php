<?php

namespace Tiimber\Loggers;

use const Tiimber\Consts\LogLevel\{EMERGENCY, ALERT, CRITICAL, ERROR, WARNING, NOTICE, INFO, DEBUG};

use Tiimber\Exceptions\Exception;

abstract class AbstractLogger
{
  const LEVELS = [
    EMERGENCY => 7, 
    ALERT => 6,
    CRITICAL => 5,
    ERROR => 4,
    WARNING => 3,
    NOTICE => 2,
    INFO => 1,
    DEBUG => 0
  ];
  
  private $base;
  
  public function setBaseLevel($level)
  {
    if (!isset(self::LEVELS[$level])) {
      throw new Exception('Bad bese level for loger configuration');
    }
    $this->base = $level;
  }
  
  public function isLoggable($level)
  {
    return isset(self::LEVELS[$level]) ? (self::LEVELS[$level] >= self::LEVELS[$this->Base]) : false;
  }
}
