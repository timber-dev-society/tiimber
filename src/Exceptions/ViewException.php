<?php
namespace Tiimber\Exceptions;

use Tiimber\Exceptions\Exception;

class ViewException extends Exception
{
  const BAD_REQUEST = 400;

  const UNAUTORIZED = 401;

  const FORBIDEN = 403;

  const NOT_FOUND = 404;

  public function __construct($code, $message)
  {
    parent::__construct($message, $code);
  }
}
