<?php
namespace KissPHP;

use KissPHP\Application;

class Mailer
{
  //-setFrom(array('john@doe.com' => 'John Doe')
  public function __construct($sendTo, $sendFrom, $subject, $message) {

    //SI plusieurs ARRAY $sendTo -> Envoi à plusieurs personnes

    $mail = Swift_Message::newInstance($subject)
    ->setFrom($sendFrom)
    ->setTo($sendTo)
    ->setBody($message)
    ;


    // Send the message
    $result = $mailer->send($mail);
  }

  private function send ($mail) {

    $transport = Swift_SmtpTransport::newInstance('smtp.example.org', 25)
    ->setUsername('your username')
    ->setPassword('your password')
    ;

    $mailer = Swift_Mailer::newInstance($transport);

    // Send the message
    $result = $mailer->send($message);
  }
}