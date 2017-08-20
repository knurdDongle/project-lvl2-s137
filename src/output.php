<?php

namespace DiffFinder\output;

function output(array $AST, $spaces = '')
{
    $line = '';

    foreach ($AST as $array) {
        if ($array['type'] === 'nested') {
            $children = output($array['children'], '  ');
            $line .= "    \"{$array['key']}\": {\n$children    }\n";
        } elseif ($array['type'] === 'unchanged') {
            $value = boolToText($array['from']);
            $line .= "$spaces$spaces$spaces  \"{$array['key']}\": {$value}\n";
        } elseif ($array['type'] === 'removed') {
            if (is_array($array['from'])) {
                $from = unpackArray($array['from'], $spaces);
                $line .= "$spaces$spaces  - \"{$array['key']}\": {\n{$from}$spaces$spaces    }\n";
            } else {
                $value = boolToText($array['from']);
                $line .= "$spaces$spaces$spaces- \"{$array['key']}\": {$value}\n";
            }
        } elseif ($array['type'] === 'added') {
            if (is_array($array['from'])) {
                $from = unpackArray($array['from'], $spaces);
                $line .= "$spaces$spaces  + \"{$array['key']}\": {\n{$from}$spaces$spaces    }\n";
            } else {
                $value = boolToText($array['from']);
                $line .= "$spaces$spaces$spaces+ \"{$array['key']}\": {$value}\n";
            }
        } elseif ($array['type'] === 'changed') {
//            if (is_array($array['from'])) {
//                $from = unpackArray($array['from']);
//                $line .= "$spaces+ \"{$array['key']}\": {\n{$from}}\n";
//            } else {
            $value1 = boolToText($array['from']);
            $value2 = boolToText($array['to']);
            $line .= "$spaces$spaces$spaces- \"{$array['key']}\": {$value1}\n";
            $line .= "$spaces$spaces$spaces+ \"{$array['key']}\": {$value2}\n";
//            }
        }
    }

    return $line;
}


function unpackArray($array, $spaces) {
    return array_reduce(array_keys($array), function ($acc, $key) use ($array, $spaces) {
        if ($array[$key] !== null) {
            $acc .= "$spaces$spaces        \"{$key}\": \"{$array[$key]}\"\n";
            return $acc;
        }
    }, '');
}


function boolToText($value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    }
    return "\"$value\"";
}