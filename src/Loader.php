<?php
namespace Tiimber;

use const Tiimber\Consts\Scopes\{LAYOUT, VIEW, HELPER, ACTION, PAGE};
use const Tiimber\Consts\Folder\DS;

use Tiimber\Traits\FolderResolverTrait;

class Loader
{
  use FolderResolverTrait;

  public function __construct(string $namespace)
  {
    //$this->viewsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->pagesLoading($namespace, $this->getBaseDir() . DS . $namespace);
    //$this->actionsLoading($namespace, $this->getBaseDir() . DS . $namespace);
    $this->helpersLoading($namespace, $this->getBaseDir() . DS . $namespace);
    //$this->layoutsLoading($namespace, $this->getBaseDir() . DS . $namespace);
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

  private function pagesLoading($namespace, $folder)
  {
    Memory::create(PAGE);
    $this->load(PAGE, $namespace, $folder);
  }

  private function viewsLoading($namespace, $folder)
  {
    Memory::create(VIEW);
    $this->load(VIEW, $namespace, $folder);
  }
  
  private function actionsLoading($namespace, $folder)
  {
    Memory::create(ACTION);
    $this->load(ACTION, $namespace, $folder);
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

  private function load($scope, $namespace, $folder)
  {
    if (is_dir($folder . DS . ucfirst($scope))) {
      $this->loadFromDir(
        $scope,
        $folder . DS . ucfirst($scope),
        '\\' . $namespace . '\\' . ucfirst($scope) . '\\'
      );
      $this->getDir(
        $folder . DS . ucfirst($scope),
        function (string $dirname) use ($scope, $folder, $namespace) {
          $this->loadFromDir(
            $scope,
            $folder . DS . ucfirst(PAGE) . DS . $dirname,
            '\\' . $namespace . '\\' . ucfirst($scope) . '\\' . $dirname . '\\'
          );
        }
      );
    }
  }
}
