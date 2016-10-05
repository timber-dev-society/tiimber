<?php
namespace Tiimber\Traits;

use const Tiimber\Consts\Scopes\HTTPS;
use const Tiimber\Consts\Http\{PORT, HOST, CODE, HEADER};

trait HeaderTrait
{
  public function setSatusCode(int $code)
  {
    Memory::get(HTTP)->set(CODE, $code);
  }
  
  public function addHeader(string $key, $value)
  {
    $headers = Memory::get(HTTP)->get(HEADER);
    $headers[$key] = $value;
    Memory::get(HTTP)->set(HEADER, $headers);
  }
  
  public function setContentType($contentType)
  {
    $this->addHeader('Content-Type', $contentType);
  }
}