<?php

namespace DiffFinder;

use function \Funct\Collection\union;

function findDiff($array1, $array2)
{
    $resultArray = arraysDiff($array1, $array2);

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


function arraysDiff($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));

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


function arrayToText($array, $spaces = '')
{
    $result = implode("\n", array_map(function ($key, $value) use ($spaces) {
//        if (is_array($value)) {
//            return arrayToText($value, '');
//        }
        $value = boolToText($value);
        return "$spaces$key: $value";
    }, array_keys($array), $array));

    return "{\n$result\n}\n";
}

/*
function arraysDiff2($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));
//    print_r($unionArraysKeys);

    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if ($array1[$key] === $array2[$key]) {
                $acc["    $key"] = $array1[$key];
                return $acc;
            } elseif (is_array($array1[$key])) {
                $acc["    $key"] = arrayToJSON(arraysDiff2($array1[$key], $array2[$key]), '    ');
                return $acc;
            } else {
                $acc["  + $key"] = $array2[$key];
                $acc["  - $key"] = $array1[$key];
                return $acc;
            }
        } elseif (array_key_exists($key, $array2) && !array_key_exists($key, $array1)) {
            if (is_array($array2[$key])) {
                $acc["  + $key"] = arrayToJSON($array2[$key], '        ');
                return $acc;
            }
            $acc["  + $key"] = $array2[$key];
            return $acc;
        }
        if (is_array($array1[$key])) {
            $acc["  - $key"] = arrayToJSON($array1[$key], '        ');
            return $acc;
        }
        $acc["  - $key"] = $array1[$key];
        return $acc;
    }, []);
}


function arrayToJSON($array)
{
    return json_encode($array, JSON_PRETTY_PRINT);
}
*/
