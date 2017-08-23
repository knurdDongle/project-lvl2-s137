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


function buildLine($isNested, $spaces, $mark, $key, $value)
{
    $half1 ="$spaces$mark \"$key\": ";

    if ($isNested) {
        $half2 = "{\n{$value}$spaces  }\n";
    } else {
        $value = boolToText($value);
        $half2 = ($value !== 'true' && $value !== 'false') ? "\"$value\"\n" : "$value\n";
    }

    return $half1 . $half2;
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


function outputPretty($AST, $depth = 0)
{
    $spaces = str_repeat(' ', $depth * 4 + 2);

    $result = array_reduce($AST, function ($acc, $array) use ($spaces, $depth) {
        if ($array['isNested'] === true) {
            if ($array['changeType'] === 'unchanged') {
                $value = outputPretty($array['from'], $depth + 1);
                $acc .= buildLine(true, $spaces, " ", $array['key'], $value);
            } elseif ($array['changeType'] === 'changed') {
                $value = outputPretty($array['from'], 1);
                $acc .= buildLine(true, $spaces, " ", $array['key'], $value);
            } elseif ($array['changeType'] === 'removed') {
                $value = outputPretty($array['from'], $depth + 1);
                $acc .= buildLine(true, $spaces, "-", $array['key'], $value);
            } elseif ($array['changeType'] === 'added') {
                $value = outputPretty($array['from'], $depth + 1);
                $acc .= buildLine(true, $spaces, "+", $array['key'], $value);
            }
        } else {
            if ($array['changeType'] === 'unchanged') {
                $acc .= buildLine(false, $spaces, " ", $array['key'], $array['from']);
            } elseif ($array['changeType'] === 'changed') {
                $acc .= buildLine(false, $spaces, "-", $array['key'], $array['from']);
                $acc .= buildLine(false, $spaces, "+", $array['key'], $array['to']);
            } elseif ($array['changeType'] === 'removed') {
                $acc .= buildLine(false, $spaces, "-", $array['key'], $array['from']);
            } elseif ($array['changeType'] === 'added') {
                $acc .= buildLine(false, $spaces, "+", $array['key'], $array['from']);
            }
        }

        return $acc;
    }, '');

    return $result;
}


function outputPlain($AST, $parents = '')
{
    $result = array_reduce($AST, function ($acc, $array) use ($parents) {
        if ($array['isNested'] === true) {
            if ($array['changeType'] === 'changed') {
                $acc .= outputPlain($array['from'], "$parents{$array['key']}.");
            } elseif ($array['changeType'] === 'removed') {
                $acc .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['changeType'] === 'added') {
                $acc .= buildLinePlain('added', $parents . $array['key'], 'complex value');
            }
        } else {
            if ($array['changeType'] === 'changed') {
                $acc .= buildLinePlain('changed', $parents . $array['key'], $array['from'], $array['to']);
            } elseif ($array['changeType'] === 'removed') {
                $acc .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['changeType'] === 'added') {
                $acc .= buildLinePlain('added', $parents . $array['key'], $array['from']);
            }
        }

        return $acc;
    }, '');

    return $result;
}

function outputJSON($AST)
{
    return json_encode($AST);
}
