<?php
namespace Tiimber\Tests;

use PHPUnit_Framework_TestCase;

use org\bovigo\vfs\vfsStream;

use Tiimber\Application;
use Tiimber\Traits\LoggerTrait;
use Tiimber\Traits\FolderResolverTrait;

use stdClass;

class LoggerTest extends PHPUnit_Framework_TestCase
{
  use LoggerTrait;

  public function testDefaultLoggerFiles()
  {
    $root = vfsStream::setup();
    (new Application())->setRoot($root->url());
    $message = 'foo bar';
    $date = date("d/m/Y ~ G\:i ");
    $this->notice($message);
    $this->assertTrue($root->hasChild('log'));
    $filepath = $this->getBaseDir() . "/log/tiimber.log";
    $this->assertEquals($date . '[notice] ' . $message, file_get_contents($filepath));
  }
}
