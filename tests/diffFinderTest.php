<?php
namespace Diff\Tests;

//use \PHPUnit\Framework\TestCase;

class DiffFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindDiff()
    {
        $findDiffResult = "{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
";
        $this->assertEquals("$findDiffResult", \DiffFinder\findDiff('fixtures/before.json', 'fixtures/after.json'));
    }
}
