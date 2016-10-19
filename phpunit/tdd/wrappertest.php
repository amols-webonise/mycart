<?php
require_once('./../../demo1.php');
require_once './../vendor/autoload.php';
 
class WrapperTest extends PHPUnit_Framework_TestCase {
 
    private $wrapper;

    function setUp(){
        $this->wrapper = new Wrapper();
    }

    function testDoesNotWrapAShorterThanMaxCharsWord() {
        $this->assertEquals('word', $this->wrapper->wrap('word', 5));
    }

    function testItShouldWrapAnEmptyString(){
        $this->assertEquals('', $this->wrapper->wrap('', 0));
    }

    function testItDoesNotWrapAShortEnoughWord() {
        $textToBeParsed = 'word';
        $maxLineLength = 5;
        $this->assertEquals($textToBeParsed, $this->wrapper->wrap($textToBeParsed, $maxLineLength));
    }

    function testItWrapsAWordLongerThanLineLength() {
        $textToBeParsed = 'alongword';
        $maxLineLength = 5;
        $this->assertEquals("along\nword", $this->wrapper->wrap($textToBeParsed, $maxLineLength));
    }

    function testItWrapsAWordSeveralTimesIfItsTooLong() {
        $textToBeParsed = 'averyverylongword';
        $maxLineLength = 5;
        $this->assertEquals("avery\nveryl\nongwo\nrd", $this->wrapper->wrap($textToBeParsed, $maxLineLength));
    }
}

