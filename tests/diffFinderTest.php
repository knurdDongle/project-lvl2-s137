<?php
namespace Tests;

//use \PHPUnit\Framework\TestCase;

class DiffFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindDiff()
    {
        $findDiffResult = "\n{\n    host: hexlet.io\n  - proxy: 123.234.53.22\n  - timeout: 50\n  + timeout: 20\n  + verbose: true\n}\n";
        $this->assertEquals("$findDiffResult", \DiffFinder\findDiff('tests/before.json', 'tests/after.json'));
    }
}
