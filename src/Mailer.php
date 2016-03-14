<?php
namespace Tiimber;

use Tiimber\Interfaces\MailDriverInterface;

class Mailer
{
  private static $instance;

  private $driver;

  private function __construct()
  {
    $config = Config::get('mailer');

    if (!$config) {
      throw new Exception('No mailer configuration');
    }

    $driver = '\\' . $config->driver;
    $this->driver = new $driver();

    if (!$this->driver instanceof MailDriverInterface) {
      throw new Exception($driver . ' must implement MailDriverInterface');
    }
    $this->driver->setConfig($config);
  }

  /**
   * @return MailDriverInterface;
   */
  public static function newMail()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance->driver;
  }
}