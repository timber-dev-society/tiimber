<?php
namespace Tiimber\Tests\Traits;

use PHPUnit_Framework_TestCase;

use Tiimber\{Memory, Traits\LoggerTrait};

use const Tiimber\Consts\Events\LOG;

class LoggerTraitTest extends PHPUnit_Framework_TestCase
{
  use LoggerTrait;

  public function testDefaultLoggerFiles()
  {
    $message = 'foo bar';
    $date = date("d/m/Y ~ G\:i ");
    $test = (object)[
      'level' => '',
      'message' => ''
    ];
  
    Memory::events()->on(LOG, function (string $level, string $message) use ($test) {
      $test->level = $level;
      $test->message = $message;
    });

    $this->notice($message);
    usleep(10000);
    $this->assertEquals('notice', $test->level);
    $this->assertEquals($date . '[notice] ' . $message, $test->message);
  }
}
