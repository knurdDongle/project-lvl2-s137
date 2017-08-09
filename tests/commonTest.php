<?php
namespace Tests;

//use \PHPUnit\Framework\TestCase;

class CommonTest extends \PHPUnit_Framework_TestCase
{
    public function testCommon()
    {
        $startGenDiffResult = "Usage:\n  gendiff (-h|--help)\n  gendiff [--format <fmt>] <firstFile> <secondFile>";
        $this->assertEquals("$startGenDiffResult", \Common\startGenDiff());
    }
}
