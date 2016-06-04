<?php
namespace Tiimber;

use Tiimber\Interfaces\MailDriverInterface;

/**
 * Basic class to create e-mails.
 */
class Mailer
{
  private static $instance;

  private $driver;

  private function __construct()
  {
    $config = Config::get('mailer', false);

    if (!$config) {
      throw new Exception('No mailer configuration');
    }

    $driver = $config->get('driver', false);
    if (!$driver) {
      throw new Exception('No mail driver found');
    }

    $this->driver = new $driver();
    if (!$this->driver instanceof MailDriverInterface) {
      throw new Exception($driver . ' must implement MailDriverInterface');
    }

    $this->driver->setConfig($config);
  }

  /**
   * @return MailDriverInterface;
   */
  public static function newMail(): MailDriverInterface
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance->driver;
  }
}
