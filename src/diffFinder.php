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


/*function arraysDiff2($array1, $array2)
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
}*/


/*function arraysDiff2($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));

    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = [
                        'key' => $key,
                        'isNested' => true,
                        'changeType' => 'unchanged',
                        'from' => $array1[$key],
                        'to' => null
                    ];
                } else {
                    $acc[] = [
                        'key' => $key,
                        'isNested' => true,
                        'changeType' => 'changed',
                        'from' => arraysDiff2($array1[$key], $array2[$key]),
                        'to' => null
                    ];
                }
            } else {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = [
                        'key' => $key,
                        'isNested' => false,
                        'changeType' => 'unchanged',
                        'from' => $array1[$key],
                        'to' => null
                    ];
                } else {
                    $acc[] = [
                        'key' => $key,
                        'isNested' => false,
                        'changeType' => 'changed',
                        'from' => $array1[$key],
                        'to' => $array2[$key]
                    ];
                }
            }
        } elseif (array_key_exists($key, $array1)) {
            if (is_array($array1[$key])) {
                $acc[] = [
                    'key' => $key,
                    'isNested' => true,
                    'changeType' =>
                        'removed',
                    'from' => $array1[$key],
                    'to' => null];
            } else {
                $acc[] = [
                    'key' => $key,
                    'isNested' => false,
                    'changeType' => 'removed',
                    'from' => $array1[$key],
                    'to' => null
                ];
            }
        } elseif (is_array($array2[$key])) {
            $acc[] = [
                'key' => $key,
                'isNested' => true,
                'changeType' => 'added',
                'from' => $array2[$key],
                'to' => null
            ];
        } else {
            $acc[] = [
                'key' => $key,
                'isNested' => false,
                'changeType' => 'added',
                'from' => $array2[$key],
                'to' => null
            ];
        }
        return $acc;
    }, []);
}*/


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
