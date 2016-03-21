<?php
namespace Tiimber\Interfaces;

use Tiimber\ParameterBag;

/**
 * To: recipients -- the primary recipients (required)
 * Cc: recipients -- receive a copy of the message (optional)
 * Bcc: recipients -- hidden from other recipients (optional)
 */
interface MailDriverInterface
{
  /**
   * Entry point to receive Mailer configuration
   */
  public function setConfig(ParameterBag $config);

  /**
   * Set e-mail body content
   */
  public function setBody($message);

  /**
   * set e-mail subject content
   */
  public function setSubject($subject);

  /**
   * Set the e-mail sender
   *
   * @param $addresses array
   */
  public function setFrom(array $addresses);

  /**
   *
   */
  public function setTo(array $addresses);

  public function addTo($address, $name = null);

  public function setCc(array $addresses);

  public function addCc($address, $name = null);

  public function setBcc(array $addresses);

  public function addBcc($address, $name = null);

  public function attach($filepath);

  public function send();
}