<?php
namespace Tests;

use \PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testDiffFinder()
    {
        $findDiffResult = "{\n    host: hexlet.io\n  - proxy: 123.234.53.22\n  - timeout: 50\n  + timeout: 20\n  + verbose: true\n}";
        $this->assertEquals("$findDiffResult", \DiffFinder\findDiff('before.json', 'after.json'));
    }
}
