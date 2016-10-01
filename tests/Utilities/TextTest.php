<?php
namespace Tiimber\Tests\Utilities;

use Tiimber\Utilities\Text;

use PHPUnit_Framework_TestCase;

class TextTest extends PHPUnit_Framework_TestCase
{
  public function testSlugify()
  {
    if (
      !@iconv('UTF-8', 'US-ASCII//TRANSLIT', 'â') ||
      '?' == @iconv('UTF-8', 'US-ASCII//TRANSLIT', 'â')
    ) {
      $this->markTestSkipped(
        'Find a way to fix docker box and iconv bug.'
      );
    }
    $this->assertEquals('abc123', Text::slugify('abc123'));
    $this->assertEquals('abc123', Text::slugify('AbC123'));
    $this->assertEquals('aceun', Text::slugify('âçéüñ'));
    $this->assertEquals('a-b-c-1-2-3', Text::slugify('a b\\c/1,2%3'));
    $this->assertEquals('1', Text::slugify(':;.,?1'));
    $this->assertEquals('n-a', Text::slugify(':;.,?'));
  }

  public function testCropText()
  {
    $this->assertEquals('abcde', Text::crop('abcde', 5, false));
    $this->assertEquals('ab...', Text::crop('abcdef', 5, false));
    $this->assertEquals(5, strlen(Text::crop('abcdef gh', 5, false)));
  }

  public function testCropTextWithPreseveWord()
  {
    $this->assertEquals('foo bar', Text::crop('foo bar', 7));
    $this->assertEquals('foo...', Text::crop('foo bar baz', 2));
    $this->assertEquals('foo bar...', Text::crop('foo bar baz', 6));
  }
}
