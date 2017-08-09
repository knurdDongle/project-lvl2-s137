<?php

namespace DiffFinder;

function findDiff($firstFile, $secondFile)
{
    $firstFileArray = json_decode(file_get_contents($firstFile), true);
    $secondFileArray = json_decode(file_get_contents($secondFile), true);

    $c = \Funct\Collection\union($firstFileArray, $secondFileArray);

    $resultArray = array_map(function ($key) use ($secondFileArray, $firstFileArray, $c) {
        if (array_key_exists($key, $secondFileArray) && array_key_exists($key, $firstFileArray)) {
            if ($secondFileArray[$key] == $firstFileArray[$key]) {
                $value = boolToText($secondFileArray[$key]);
                return "  $key: $value";
            } else {
                $value1 = boolToText($secondFileArray[$key]);
                $value2 = boolToText($firstFileArray[$key]);
                return "+ $key: $value1\n- $key: $value2";
            }
        } elseif (array_key_exists($key, $secondFileArray) && !array_key_exists($key, $firstFileArray)) {
            $value = boolToText($secondFileArray[$key]);
            return "+ $key: $value";
        } else {
            $value = boolToText($firstFileArray[$key]);
            return "- $key: $value";
        }
    }, array_keys($c));


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
