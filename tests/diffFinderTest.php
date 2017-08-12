<?php
namespace Diff\Tests;

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
        $array1 = ['host' => 'hexlet.io', 'timeout' => 50, 'proxy' => '123.234.53.22'];
        $array2 = ['timeout' => 20, 'verbose' => true, 'host' => 'hexlet.io'];

        $this->assertEquals("$findDiffResult", \DiffFinder\findDiff($array1, $array2));
    }
}
