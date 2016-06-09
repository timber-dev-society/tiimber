<?php
namespace Tiimber\Traits;

use Tiimber\Memory;
use const Tiimber\Folder\{SCOPE, BASE, CONFIG, RESOURCE, CACHE, DS};

trait FolderResolverTrait
{
  public function getBaseDir()
  {
    return Memory::get(SCOPE)->get(BASE);
  }

  public function getConfigDir()
  {
    return Memory::get(SCOPE)->get(CONFIG, false)
        ?: Memory::get(SCOPE)->get(BASE) . DS . CONFIG . DS;
  }

  public function getResourceDir()
  {
    return Memory::get(SCOPE)->get(RESOURCE, false)
        ?: Memory::get(SCOPE)->get(BASE) . DS . RESOURCE . DS;
  }

  public function getCacheDir()
  {
    return Memory::get(SCOPE)->get(CACHE, false)
        ?: Memory::get(SCOPE)->get(BASE) . DS . CACHE . DS;
  }
}
