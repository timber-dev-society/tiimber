<?php
namespace Tiimber;

use const Tiimber\Memory\Scopes\{LAYOUT, VIEW, HELPER, ACTION};
use const Tiimber\Folder\DS;

use Tiimber\Traits\FolderResolverTrait;

class Loader
{
  use FolderResolverTrait;

  public function __construct(string $namespace)
  {
    $this->viewsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->actionsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->helpersLoading($namespace, $this->getBaseDir() . DS . $namespace);
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
    Memory::create(VIEW);
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
  
  private function actionsLoading($namespace, $folder)
  {
    Memory::create(ACTION);
    if (is_dir($folder . DS . ucfirst(ACTION))) {
      $this->loadFromDir(
        ACTION,
        $folder . DS . ucfirst(ACTION),
        '\\' . $namespace . '\\' . ucfirst(ACTION) . '\\'
      );
      $this->getDir(
        $folder . DS . ucfirst(ACTION),
        function (string $dirname) use ($folder, $namespace) {
          $this->loadFromDir(
            ACTION,
            $folder . DS . ucfirst(ACTION) . DS . $dirname,
            '\\' . $namespace . '\\' . ucfirst(ACTION) . '\\' . $dirname . '\\'
          );
        }
      );
    }
  }
  
  private function helpersLoading($namespace, $folder)
  {
    Memory::create(HELPER);
    if (is_dir($folder . DS . ucfirst(HELPER))) {
      $this->loadFromDir(
        HELPER,
        $folder . DS . ucfirst(HELPER),
        '\\' . $namespace . '\\' . ucfirst(HELPER) . '\\'
      );
    }
  }

  private function layoutsLoading($namespace, $folder)
  {
    if (is_dir($folder . DS . ucfirst(LAYOUT))) {
      $this->loadFromDir(
        LAYOUT,
        $folder . DS . ucfirst(LAYOUT),
        '\\' . $namespace . '\\' . ucfirst(LAYOUT) . '\\'
      );
    }
  }
}
