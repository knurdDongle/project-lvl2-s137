<?php
namespace Diff\Tests;

class DiffFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindDiff()
    {
        $findDiffResult = '    "host": "hexlet.io"
  - "timeout": "50"
  + "timeout": "20"
  - "proxy": "123.234.53.22"
  + "verbose": true
';
        $array1 = ['host' => 'hexlet.io', 'timeout' => 50, 'proxy' => '123.234.53.22'];
        $array2 = ['timeout' => 20, 'verbose' => true, 'host' => 'hexlet.io'];

        $this->assertEquals("$findDiffResult", \DiffFinder\diff\findDiff($array1, $array2));
    }

    public function testNestedFindDiff()
    {
        $array1 = ["common" => [
                "setting1" => "Value 1",
                "setting2" => "200",
                "setting3" => true,
                "setting6" => [
                    "key" => "value"
                ]
        ],
            "group1" => [
                "baz" => "bas",
                "foo" => "bar"
                ],
            "group2" => [
                "abc" => "12345"
            ]];

        $array2 =["common" => [
            "setting1" => "Value 1",
            "setting3" => true,
            "setting4" => "blah blah",
            "setting5" => [
                "key5" => "value5"
            ]
        ],
            "group1" => [
                "foo" => "bar",
                "baz" => "bars"
            ],
            "group3" => [
                "fee" => "100500"
            ]];

        $findDiffResult = '    "common": {
        "setting1": "Value 1"
      - "setting2": "200"
        "setting3": true
      - "setting6": {
            "key": "value"
        }
      + "setting4": "blah blah"
      + "setting5": {
            "key5": "value5"
        }
    }
    "group1": {
      - "baz": "bas"
      + "baz": "bars"
        "foo": "bar"
    }
  - "group2": {
        "abc": "12345"
    }
  + "group3": {
        "fee": "100500"
    }
';

        $this->assertEquals("$findDiffResult", \DiffFinder\diff\findDiff($array1, $array2));
    }
}
