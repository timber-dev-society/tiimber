<?php
namespace Tiimber\Tests;

use Tiimber\Application;
use Tiimber\Config;
use Tiimber\ParameterBag;

use PHPUnit_Framework_TestCase;

use stdClass;

class ConfigTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    $application = new Application();
    $application->setConfigDir(__DIR__ . '/Config/');
  }

  public function testLoadConfigFiles()
  {
    $value = Config::get('controllers');
    $this->assertInstanceOf('Tiimber\\ImmutableBag', $value);
    $this->assertEquals('Tiimber\\Tests\\Application\\Controllers\\Index', $value->get('Index'));
  }

  public function testLoadConfigFolders()
  {
    $values = Config::get('routes');
    $count = 0;
    foreach ($values as $key => $value) {
      $this->assertTrue(in_array($key, ['basic_route', 'route_with_param', 'route_with_protocol']));
      $this->assertTrue($value instanceof stdClass);
      $count++;
    }

    $this->assertEquals(3, $count);
  }

  public function testNonExistentConfig()
  {
    $this->assertTrue(Config::get('undefined', true));
    $this->assertNull(Config::get('undefined'));
  }
}