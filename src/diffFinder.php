<?php

namespace DiffFinder\diff;

use function \Funct\Collection\union;

function findDiff($array1, $array2)
{
    $resultArray = arraysDiff3($array1, $array2);

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


function arraysDiff3($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));

    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = buildArray($key, true, 'unchanged', arraysDiff3($array1[$key], $array1[$key]), null);
                } else {
                    $acc[] = buildArray($key, true, 'changed', arraysDiff3($array1[$key], $array2[$key]), null);
                }
            } else {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = buildArray($key, false, 'unchanged', $array1[$key], null);
                } else {
                    $acc[] = buildArray($key, false, 'changed', $array1[$key], $array2[$key]);
                }
            }
        } elseif (array_key_exists($key, $array1)) {
            if (is_array($array1[$key])) {
                $acc[] = buildArray($key, true, 'removed', arraysDiff3($array1[$key], $array1[$key]), null);
            } else {
                $acc[] = buildArray($key, false, 'removed', $array1[$key], null);
            }
        } elseif (is_array($array2[$key])) {
            $acc[] = buildArray($key, true, 'added', arraysDiff3($array2[$key], $array2[$key]), null);
        } else {
            $acc[] = buildArray($key, false, 'added', $array2[$key], null);
        }
        return $acc;
    }, []);
}


function buildArray($key, $isNested, $changeType, $from, $to)
{
    return [
        'key'        => $key,
        'isNested'   => $isNested,
        'changeType' => $changeType,
        'from'       => $from,
        'to'         => $to
    ];
}
