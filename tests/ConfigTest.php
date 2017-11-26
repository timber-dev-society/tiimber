<?php
namespace Tiimber\Tests;

use Tiimber\{Config, ImmutableBag, Memory};

use const Tiimber\Consts\{Scopes\FOLDER, Folder\CONFIG};

use PHPUnit_Framework_TestCase;

use stdClass;

class ConfigTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    Memory::set(FOLDER)->set(CONFIG, __DIR__ . '/Config/');
  }

  public function testLoadConfigFiles()
  {
    $value = Config::get('controllers');
    $this->assertInstanceOf('Tiimber\\Bags\\ImmutableBag', $value);
    $this->assertEquals('Tiimber\\Tests\\Application\\Controllers\\Index', $value->get('Index'));
  }

  public function testLoadConfigFolders()
  {
    $values = Config::get('routes');
    $count = 0;
    foreach ($values as $key => $value) {
      $this->assertTrue(in_array($key, ['basic', 'with::param', 'with::protocol']));
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