<?php
namespace Tiimber;

use const Tiimber\Memory\Scopes\{LAYOUT, VIEW};
use const Tiimber\Folder\DS;

use Tiimber\Traits\FolderResolverTrait;

class Loader
{
  use FolderResolverTrait;

  public function __construct(string $namespace)
  {
    $this->viewsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->layoutsLoading($namespace, $this->getBaseDir() . DS . $namespace);
  }

  private function loadFromDir(string $scope, string $folder, string $namespace)
  {
    foreach(glob($folder . DS . '*.php') as $file) {
      $classname = $namespace . basename($file, '.php');
      Memory::set($scope)->set($classname, new $classname());
    }
  }

  private function getDir(string $base, callable $callback)
  {
    $scannedDir = array_diff(scandir($base), ['..', '.']);
    foreach ($scannedDir as $dir) {
      if (is_dir($base . DS . $dir)) {
        $callback($dir);
      }
    }
  }

  private function viewsLoading($namespace, $folder)
  {
    if (is_dir($folder . DS . ucfirst(VIEW))) {
      $this->loadFromDir(
        VIEW,
        $folder . DS . ucfirst(VIEW),
        '\\' . $namespace . '\\' . ucfirst(VIEW) . '\\'
      );
      $this->getDir(
        $folder . DS . ucfirst(VIEW),
        function (string $dirname) use ($folder, $namespace) {
          $this->loadFromDir(
            VIEW,
            $folder . DS . ucfirst(VIEW) . DS . $dirname,
            '\\' . $namespace . '\\' . ucfirst(VIEW) . '\\' . $dirname . '\\'
          );
        }
      );
    }
  }

  private function layoutsLoading($namespace, $folder)
  {
    if (is_dir($folder . DS . 'Layouts')) {
      $this->loadFromDir(
        LAYOUT,
        $folder . DS . ucfirst(LAYOUT),
        '\\' . $namespace . '\\' . ucfirst(LAYOUT) . '\\'
      );
    }
  }
}
