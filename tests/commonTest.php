<?php
namespace Tests;

use \PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCommon()
    {
        $startGenDiffResult = "Usage:\n  gendiff (-h|--help)\n  gendiff [--format <fmt>] <firstFile> <secondFile>";
        $this->assertEquals("$startGenDiffResult", \Common\startGenDiff());
    }
}
