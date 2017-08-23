<?php

namespace DiffFinder\output;

function boolToText($value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    }
    return $value;
}


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
                $result .= buildLine(false, $spaces, " ", $array['key'], $array['from']);
            } elseif ($array['changeType'] === 'changed') {
                $result .= buildLine(false, $spaces, "-", $array['key'], $array['from']);
                $result .= buildLine(false, $spaces, "+", $array['key'], $array['to']);
            } elseif ($array['changeType'] === 'removed') {
                $result .= buildLine(false, $spaces, "-", $array['key'], $array['from']);
            } elseif ($array['changeType'] === 'added') {
                $result .= buildLine(false, $spaces, "+", $array['key'], $array['from']);
            }
        }
    }

    return $result;
}


function buildLine($isNested, $spaces, $mark, $key, $value)
{
    $half1 ="$spaces$mark \"{$key}\": ";

    if ($isNested) {
        $half2 = "{\n{$value}$spaces  }\n";
    } else {
        $value = boolToText($value);
        $half2 = ($value !== 'true' && $value !== 'false') ? "\"{$value}\"\n" : "{$value}\n";
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
                $result .= buildLinePlain('added', $parents . $array['key'], 'complex value');
            }
        } else {
            if ($array['changeType'] === 'changed') {
                $result .= buildLinePlain('changed', $parents . $array['key'], $array['from'], $array['to']);
            } elseif ($array['changeType'] === 'removed') {
                $result .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['changeType'] === 'added') {
                $result .= buildLinePlain('added', $parents . $array['key'], $array['from']);
            }
        }
    }

    return $result;
}


function buildLinePlain($changeType, $property, $value1 = '', $value2 = '')
{
    $line = '';
    $value1 = boolToText($value1);
    $value2 = boolToText($value2);

    if ($changeType === 'removed') {
        $line = "'$property' was $changeType";
    } elseif ($changeType === 'added') {
        $line = "'$property' was $changeType with value: '$value1'";
    } elseif ($changeType === 'changed') {
        $line = "'$property' was $changeType. From '$value1' to '$value2'";
    }

    return "Property $line\n";
}
