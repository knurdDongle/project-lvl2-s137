<?php
namespace Tests;

//use \PHPUnit\Framework\TestCase;

class DiffFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindDiff()
    {
        $findDiffResult = "{
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
";
        $this->assertEquals("$findDiffResult", \DiffFinder\findDiff('tests/before.json', 'tests/after.json'));
    }
}
