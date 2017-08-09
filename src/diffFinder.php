<?php

namespace DiffFinder;

function findDiff($firstFile, $secondFile)
{
    $firstFileArray = json_decode(file_get_contents($firstFile), true);
    $secondFileArray = json_decode(file_get_contents($secondFile), true);

    $unionArrays = \Funct\Collection\union($firstFileArray, $secondFileArray);

    $resultArray = arrayDiff($firstFileArray, $secondFileArray, $unionArrays);

    return arrayToText($resultArray);
}


function boolToText($key)
{
    if ($key === true) {
        return 'true';
    } elseif ($key === false) {
        return 'false';
    }
    return $key;
}


function arrayToText($array)
{
    $result = implode("\n", $array);
    return "{\n$result\n}\n";
}


function arrayDiff($firstArray, $secondArray, $unionArrays)
{
    return array_map(function ($key) use ($secondArray, $firstArray) {
        if (array_key_exists($key, $secondArray) && array_key_exists($key, $firstArray)) {
            if ($secondArray[$key] == $firstArray[$key]) {
                $value = boolToText($secondArray[$key]);
                return "    $key: $value";
            }
            $value1 = boolToText($secondArray[$key]);
            $value2 = boolToText($firstArray[$key]);
            return "  + $key: $value1\n  - $key: $value2";
        } elseif (array_key_exists($key, $secondArray) && !array_key_exists($key, $firstArray)) {
            $value = boolToText($secondArray[$key]);
            return "  + $key: $value";
        }
        $value = boolToText($firstArray[$key]);
        return "  - $key: $value";
    }, array_keys($unionArrays));
}