<?php
namespace Tiimber\Tests;

use PHPUnit_Framework_TestCase;

use org\bovigo\vfs\vfsStream;

use Tiimber\Application;
use Tiimber\Logger;

use stdClass;

class LoggerTest extends PHPUnit_Framework_TestCase
{
  use Logger;

  public function testDefaultLoggerFiles()
  {
    $root = vfsStream::setup();
    (new Application())->setRoot($root->url());
    $message = 'foo bar';
    $date = date("d/m/Y ~ G\:i ");
    $this->notice($message);
    $this->assertTrue($root->hasChild('log'));
    $filepath = Application::getBaseDir() . "/log/tiimber.log";
    $this->assertEquals($date . '[notice] ' . $message, file_get_contents($filepath));
  }
}
