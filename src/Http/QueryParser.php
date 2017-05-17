<?php
namespace Tiimber\Http;

class QueryParser {

  public static function parse(string $query): array
  {
    $prameters = explode('; ', $query);
    $parsed = [];
    foreach ($prameters as $parameter) {
      $parameter = explode('=', $parameter);
      if (count($parameter) > 1) {
        $parsed[$parameter[0]] = $parameter[1]; 
      }
    }
    return $parsed;
  }
}
