<?php
namespace KissPHP\Tests;

use KissPHP\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals('abc', Utility::cropText('abc', 4));
        $this->assertEquals('ab...', Utility::cropText('abc', 2));
    }
}