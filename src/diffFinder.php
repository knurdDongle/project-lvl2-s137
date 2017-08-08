<?php

namespace DiffFinder;

function findDiff($firstFile, $secondFile)
{
    $firstFileArray = json_decode(file_get_contents($firstFile), true);
    $secondFileArray = json_decode(file_get_contents($secondFile), true);

    $resultArray = [];

    $intersection = array_intersect_assoc($firstFileArray, $secondFileArray);
    $diffArray1 = array_diff_assoc($firstFileArray, $secondFileArray);
    $diffArray2 = array_diff_assoc($secondFileArray, $firstFileArray);

    $resultArray = mergeDiff($resultArray, $diffArray1, '+');
    $resultArray = mergeDiff($resultArray, $diffArray2, '-');
    $resultArray = mergeDiff($resultArray, $intersection, ' ');

    uksort($resultArray, "\DiffFinder\cmp");

    $resultString = arrayToText($resultArray);

    printResultString($resultString);
}


function mergeDiff($res, $diff, $sign)
{
    foreach ($diff as $key => $value) {
        $res["$sign $key"] = $value;
    }
    return $res;
}


function cmp($key1, $key2)
{
    $key1 = strstr($key1, ' ');
    $key2 = strstr($key2, ' ');
    if ($key1 == $key2) {
        return 0;
    }
    return ($key1 < $key2) ? -1 : 1;
}


function arrayToText($array)
{
    return implode("\n", array_map(function ($key, $value) {
        return "  $key: $value";
    }, array_keys($array), $array));
}


function printResultString($resultString)
{
    echo "{\n".$resultString."\n}\n";
}
