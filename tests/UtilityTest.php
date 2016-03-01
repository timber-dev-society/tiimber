<?php
namespace KissPHP\Tests;

use KissPHP\Utility;

use PHPUnit_Framework_TestCase;

class UtilityTest extends PHPUnit_Framework_TestCase
{
  public function testSlugify()
  {
    $this->assertEquals('abc123', Utility::slugify('abc123'));
    $this->assertEquals('abc123', Utility::slugify('AbC123'));
    $this->assertEquals('aceun', Utility::slugify('âçéüñ'));
    $this->assertEquals('a-b-c-1-2-3', Utility::slugify('a b\\c/1,2%3'));
    $this->assertEquals('1', Utility::slugify(':;.,?1'));
    $this->assertEquals('n-a', Utility::slugify(':;.,?'));
  }
  
  public function testCropText()
  {
    $this->assertEquals('abcde', Utility::cropText('abcde', 5, false));
    $this->assertEquals('ab...', Utility::cropText('abcdef', 5, false));
    $this->assertEquals(5, strlen(Utility::cropText('abcdef gh', 5, false)));
  }
  
  public function testCropTextWithPreseveWord()
  {
    $this->assertEquals('foo bar', Utility::cropText('foo bar', 7));
    $this->assertEquals('foo...', Utility::cropText('foo bar baz', 2));
    $this->assertEquals('foo bar...', Utility::cropText('foo bar baz', 6));
  }
}