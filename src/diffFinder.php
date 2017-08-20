<?php

namespace DiffFinder\diff;

use function \Funct\Collection\union;

function findDiff($array1, $array2)
{
    $resultArray = arraysDiff2($array1, $array2);

//    return $resultArray;
    return \DiffFinder\output\output($resultArray);
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

/*
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


function arrayToText($array)
{
    $result = implode("\n", array_map(function ($key, $value) {
        $value = boolToText($value);
        return "$key: $value";
    }, array_keys($array), $array));

    return "{\n$result\n}";
}*/


function arraysDiff2($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));

    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if ($array1[$key] === $array2[$key]) {
                $acc[] = ['key' => $key, 'type' => 'unchanged', 'from' => $array1[$key], 'to' => null];
                return $acc;
            } elseif (is_array($array1[$key])) {
                $acc[] = ['key' => $key, 'type' => 'nested', 'children' => arraysDiff2($array1[$key], $array2[$key])];
                return $acc;
            } else {
                $acc[] = ['key' => $key, 'type' => 'changed', 'from' => $array1[$key], 'to' => $array2[$key]];
                return $acc;
            }
        } elseif (array_key_exists($key, $array2) && !array_key_exists($key, $array1)) {
//            if (is_array($array2[$key])) {
//                $acc[] = ['key' => $key, 'type' => 'added', 'from' => $array2[$key], 'to' => ''];
//                return $acc;
//            }
            $acc[] = ['key' => $key, 'type' => 'added', 'from' => $array2[$key], 'to' => null];
            return $acc;
        }
//        if (is_array($array1[$key])) {
//            $acc[] = ['key' => $key, 'type' => 'removed', 'from' => $array1[$key], 'to' => ''];
//            return $acc;
//        }
        $acc[] = ['key' => $key, 'type' => 'removed', 'from' => $array1[$key], 'to' => null];
        return $acc;
    }, []);
}
