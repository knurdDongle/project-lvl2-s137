<?php

namespace DiffFinder;

function findDiff($firstFileArray, $secondFileArray)
{
    $unionArraysKeys = \Funct\Collection\union(array_keys($firstFileArray), array_keys($secondFileArray));

    $resultArray = arraysDiff($firstFileArray, $secondFileArray, $unionArraysKeys);

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


function arraysDiff($array1, $array2, $unionArraysKeys)
{
    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if ($array1[$key] === $array2[$key]) {
                $acc["    $key"] = $array1[$key];
                return $acc;
            }
            $acc["  + $key"] = $array2[$key];
            $acc["  - $key"] = $array1[$key];
            return $acc;
        } elseif (array_key_exists($key, $array2) && !array_key_exists($key, $array1)) {
            $acc["  + $key"] = $array2[$key];
            return $acc;
        }
        $acc["  - $key"] = $array1[$key];
        return $acc;
    }, []);
}


function arrayToText($array)
{
    $result = implode("\n", array_map(function ($key, $value) {
        $value = boolToText($value);
        return "$key: $value";
    }, array_keys($array), $array));

    return "{\n$result\n}\n";
}
