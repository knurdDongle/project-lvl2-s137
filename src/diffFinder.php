<?php

namespace DiffFinder\diff;

use function \Funct\Collection\union;

function findDiff($array1, $array2, $format)
{
    $resultArray = buildAST($array1, $array2);

    if ($format === 'plain') {
        return \DiffFinder\output\outputPlain($resultArray);
    } elseif ($format === 'json') {
        return \DiffFinder\output\outputJSON($resultArray);
    }

    return "{\n".\DiffFinder\output\outputPretty($resultArray)."}";
}


function buildAST($array1, $array2)
{
    $unionArraysKeys = union(array_keys($array1), array_keys($array2));

    return array_reduce($unionArraysKeys, function ($acc, $key) use ($array1, $array2) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = buildArray($key, 'nested', $array1[$key]);
                } else {
                    $acc[] = buildArray($key, 'nested', buildAST($array1[$key], $array2[$key]));
                }
            } else {
                if ($array1[$key] === $array2[$key]) {
                    $acc[] = buildArray($key, 'unchanged', null, $array1[$key]);
                } else {
                    $acc[] = buildArray($key, 'changed', null, $array1[$key], $array2[$key]);
                }
            }
        } elseif (array_key_exists($key, $array1)) {
            if (is_array($array1[$key])) {
                $acc[] = buildArray($key, 'removed', null, $array1[$key]);
            } else {
                $acc[] = buildArray($key, 'removed', null, $array1[$key]);
            }
        } elseif (is_array($array2[$key])) {
            $acc[] = buildArray($key, 'added', null, $array2[$key]);
        } else {
            $acc[] = buildArray($key, 'added', null, $array2[$key]);
        }
        return $acc;
    }, []);
}


function buildArray($key, $type, $children, $from = null, $to = null)
{
    if ($type === 'nested') {
        return [
            'key'        => $key,
            'type'       => $type,
            'children'   => $children
        ];
    }

    return [
        'key'        => $key,
        'type'       => $type,
        'from'       => $from,
        'to'         => $to
    ];
}
