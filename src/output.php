<?php

namespace DiffFinder\output;

/*function output(array $AST, $spaces = '')
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
}*/


function boolToText($value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    }
    return "\"$value\"";
}

/*function unpackArray($array, $depth) {
    $spaces = str_repeat(' ', $depth * 4 + 6);

    return array_reduce(array_keys($array), function ($acc, $key) use ($array, $depth, $spaces) {
        if (is_array($array[$key])) {
            $value = unpackArray($array[$key], $depth + 1);
            $acc .= "$spaces  {$key}:\n{$value}";
            return $acc;
        }
        $acc .= "$spaces  {$key}: {$array[$key]}\n";
        return $acc;
    }, '');
}*/


function output($AST, $depth = 0)
{
    $result = '';

    $spaces = str_repeat(' ', $depth * 4 + 2);

    foreach ($AST as $array) {
        if ($array['isNested'] === true) {
            if ($array['changeType'] === 'unchanged') {
                $value = output($array['from'], $depth + 1);
                $result .= buildLine(true, $spaces, " ", $array['key'], $value);
            } elseif ($array['changeType'] === 'changed') {
                $value = output($array['from'], 1);
                $result .= buildLine(true, $spaces, " ", $array['key'], $value);
            } elseif ($array['changeType'] === 'removed') {
                $value = output($array['from'], $depth + 1);
                $result .= buildLine(true, $spaces, "-", $array['key'], $value);
            } elseif ($array['changeType'] === 'added') {
                $value = output($array['from'], $depth + 1);
                $result .= buildLine(true, $spaces, "+", $array['key'], $value);
            }
        } else {
            if ($array['changeType'] === 'unchanged') {
                $value = boolToText($array['from']);
                $result .= buildLine(false, $spaces, " ", $array['key'], $value);
            } elseif ($array['changeType'] === 'changed') {
                $value1 = boolToText($array['from']);
                $value2 = boolToText($array['to']);
                $result .= buildLine(false, $spaces, "-", $array['key'], $value1);
                $result .= buildLine(false, $spaces, "+", $array['key'], $value2);
            } elseif ($array['changeType'] === 'removed') {
                $value = boolToText($array['from']);
                $result .= buildLine(false, $spaces, "-", $array['key'], $value);
            } elseif ($array['changeType'] === 'added') {
                $value = boolToText($array['from']);
                $result .= buildLine(false, $spaces, "+", $array['key'], $value);
            }
        }
    }

    return "$result";
}


function buildLine($isNested, $spaces, $mark, $key, $value)
{
    $half1 ="$spaces$mark \"{$key}\": ";

    if ($isNested) {
        $half2 = "{\n{$value}$spaces  }\n";
    } else {
        $half2 = "{$value}\n";
    }

    return $half1 . $half2;
}


function outputPlain($AST, $parents = '')
{
    $result = '';

    foreach ($AST as $array) {
        if ($array['isNested'] === true) {
            if ($array['changeType'] === 'changed') {
                $result .= outputPlain($array['from'], "$parents{$array['key']}.");
            } elseif ($array['changeType'] === 'removed') {
                $result .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['changeType'] === 'added') {
                $result .= buildLinePlain('added', $parents . $array['key'], '"complex value"');
            }
        } else {
            if ($array['changeType'] === 'changed') {
                $value1 = boolToText($array['from']);
                $value2 = boolToText($array['to']);
                $result .= buildLinePlain('changed', $parents . $array['key'], $value1, $value2);
            } elseif ($array['changeType'] === 'removed') {
                $result .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['changeType'] === 'added') {
                $value = boolToText($array['from']);
                $result .= buildLinePlain('added', $parents . $array['key'], $value);
            }
        }
    }

    return "$result";
}

function buildLinePlain($changeType, $property, $value1 = '', $value2 = '')
{
    $line = '';

    if ($changeType === 'removed') {
        $line = "'$property' was $changeType";
    } elseif ($changeType === 'added') {
        $line = "'$property' was $changeType with value: $value1";
    } elseif ($changeType === 'changed') {
        $line = "'$property' was $changeType. From $value1 to $value2";
    }

    return "Property $line\n";
}
