<?php
namespace KissPHP;

use KissPHP\Application;
use KissPHP\Exception;

class Config
{
  private static $instance;

  private $config = [];

  private function __construct()
  {
    $this->loadConfigFile();
    $this->loadConfigFolder();
  }

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

  private function loadConfigFile()
  {
    $files = glob($this->getConfigdir() . '*.json', GLOB_BRACE);
    foreach($files as $file) {
      $this->config[basename($file, '.json')] = $this->readJsonFile($file);
    }
  }

  private function loadConfigFolder()
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
      $this->config[basename($folder)] = (object)$content;
    }
  }

  private function getConfigDir()
  {
    return Application::getBaseDir() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
  }
}