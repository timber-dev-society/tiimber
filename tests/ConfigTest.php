<?php
namespace KissPHP\Tests;

use KissPHP\Application;
use KissPHP\Config;
use KissPHP\ParameterBag;

use PHPUnit_Framework_TestCase;

use stdClass;

class ConfigTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    $application = new Application();
    $application->setBaseDir(__DIR__ . '/Application');
  }

  public function testLoadConfigFiles()
  {
    $value = Config::get('controllers');
    $this->assertInstanceOf('KissPHP\\ParameterBag', $value);
    $this->assertEquals('KissPHP\\Tests\\Application\\Controllers\\Index', $value->get('Index'));
  }

  public function testLoadConfigFolders()
  {
    $values = Config::get('routes');
    foreach ($values as $key => $value) {
      $this->assertTrue(in_array($key, ['basic_route', 'route_with_param', 'blog_index']));
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