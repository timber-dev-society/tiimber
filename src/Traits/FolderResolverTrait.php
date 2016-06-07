<?php
namespace Tiimber\Traits;

use Tiimber\Memory;
use Tiimber\Folder;

trait FolderResolverTrait
{
  public function getBaseDir()
  {
    return Memory::get(Folder::SCOPE)->get(Folder::BASE);
  }

  public function getConfigDir()
  {
    return Memory::get(Folder::SCOPE)->get(Folder::CONFIG, false)
        ?: Memory::get(Folder::SCOPE)->get(Folder::BASE) . Folder::DS . Folder::CONFIG . Folder::DS;
  }

  public function getResourceDir()
  {
    return Memory::get(Folder::SCOPE)->get(Folder::RESOURCE, false)
        ?: Memory::get(Folder::SCOPE)->get(Folder::BASE) . Folder::DS . Folder::RESOURCE . Folder::DS;
  }

  public function getCacheDir()
  {
    return Memory::get(Folder::SCOPE)->get(Folder::CACHE, false)
        ?: Memory::get(Folder::SCOPE)->get(Folder::BASE) . Folder::DS . Folder::CACHE . Folder::DS;
  }
}
