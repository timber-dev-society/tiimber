<?php

namespace Tiimber\Renderer;

use \Mustache_Engine;

use Tiimber\{Memory, Traits\folderResolverTrait};
use const Tiimber\Consts\Scopes\HELPER;

class Engine
{
  use FolderResolverTrait;

  private static $intance;
  
  private $engine;
  
  private function getHelper()
  {
    $helpers = [];
    foreach (Memory::get(HELPER) as $namespace => $helper) {
      $pieces = explode('\\', $namespace);
      $classname = end($pieces);
      $helpers[strtolower($classname)] = function ($text) use ($helper) {
        return $helper->render($text);
      };
    }
    return $helpers;
  }
  
  private function __construct()
  {
    $this->engine = new Mustache_Engine([
      'cache' => $this->getCacheDir(),
      'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
      'helpers' => $this->getHelper()
    ]);
  }
  
  public static function get(): Mustache_Engine
  {
    if (!self::$intance) {
      self::$intance = new self();
    }
    
    return self::$intance->engine;
  }
}