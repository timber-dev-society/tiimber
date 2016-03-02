<?php
namespace KissPHP;

use KissPHP\Application;
use KissPHP\Exception;
use KissPHP\ParameterBag;

class Config
{
  private static $instance;

  private $config = [];

  private function __construct()
  {
    $this->loadConfigFiles();
    $this->loadConfigFolders();
  }

  /**
   * Get the config file
   *
   * @param String $key config file or folder name
   * @param mixed $default
   * @return \stdClass
   */
  public static function get($key, $default = null)
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    if (isset(self::$instance->config[$key])) {
      return self::$instance->config[$key];
    }

    return $default;
  }

  private function readJsonFile($filename)
  {
    $content = json_decode(file_get_contents($filename));
    if (json_last_error() != 0) {
      throw new Exception('Bad json encoding in file: ' . $filename,  json_last_error());
    }
    return $content;
  }

  private function loadConfigFiles()
  {
    $files = glob($this->getConfigdir() . '*.json', GLOB_BRACE);
    foreach($files as $file) {
      $this->config[basename($file, '.json')] = new ParameterBag($this->readJsonFile($file));
    }
  }

  private function loadConfigFolders()
  {
    $folders = array_diff(scandir($this->getConfigdir()), ['..', '.']);

    foreach($folders as $folder) {
      if (!is_dir($this->getConfigdir() . $folder)) {
        continue;
      }
      $files = glob($this->getConfigdir() . $folder . '/*.json', GLOB_BRACE);
      $content = [];
      foreach($files as $file) {
        $content = array_merge($content, (array)$this->readJsonFile($file));
      }
      $this->config[basename($folder)] = new ParameterBag($content);
    }
  }

  private function getConfigDir()
  {
    return Application::getBaseDir() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
  }
}