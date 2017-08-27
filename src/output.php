<?php

namespace DiffFinder\output;

function outputJSON($AST)
{
    return json_encode($AST);
}


function outputPlain($AST, $parents = '')
{
    $result = array_reduce($AST, function ($acc, $array) use ($parents) {
        if ($array['type'] === 'nested') {
            $acc .= outputPlain($array['children'], "$parents{$array['key']}.");
        } else {
            if ($array['type'] === 'changed') {
                $acc .= buildLinePlain('changed', $parents . $array['key'], $array['from'], $array['to']);
            } elseif ($array['type'] === 'removed') {
                $acc .= buildLinePlain('removed', $parents . $array['key']);
            } elseif ($array['type'] === 'added') {
                if (is_array($array['from'])) {
                    $acc .= buildLinePlain('added', $parents . $array['key'], 'complex value');
                } else {
                    $acc .= buildLinePlain('added', $parents . $array['key'], $array['from']);
                }
            }
        }

        return $acc;
    }, '');

    return $result;
}


function outputPretty($AST, $depth = 0)
{
    $result = array_reduce($AST, function ($acc, $array) use ($depth) {
        if ($array['type'] === 'nested') {
            $value = outputPretty($array['children'], $depth + 1);
            $acc .= buildLine(true, " ", $array['key'], $value, $depth);
        } else {
            if ($array['type'] === 'unchanged') {
                $acc .= buildLine(false, " ", $array['key'], $array['from'], $depth);
            } elseif ($array['type'] === 'changed') {
                $acc .= buildLine(false, "-", $array['key'], $array['from'], $depth);
                $acc .= buildLine(false, "+", $array['key'], $array['to'], $depth);
            } elseif ($array['type'] === 'removed') {
                $acc .= buildLine(false, "-", $array['key'], $array['from'], $depth);
            } elseif ($array['type'] === 'added') {
                $acc .= buildLine(false, "+", $array['key'], $array['from'], $depth);
            }
        }

        return $acc;
    }, '');

    return $result;
}


function boolToText($value)
{
    if ($value === true) {
        return "true\n";
    } elseif ($value === false) {
        return "false\n";
    }

    return "\"$value\"\n";
}


function unpackArray($array)
{
    return array_reduce(array_keys($array), function ($acc, $key) use ($array) {
        $value = boolToText($array[$key]);
        $acc .= "\"$key\": $value";
        return $acc;
    }, '');
}


function buildLine($isNested, $mark, $key, $value, $depth)
{
    $spaces = str_repeat(' ', $depth * 4 + 2);

    $half1 ="$spaces$mark \"$key\": ";

    if ($isNested) {
        $half2 = "{\n$value$spaces  }\n";
    } else {
        if (!is_array($value)) {
            $half2 = boolToText($value);
        } else {
            $values = unpackArray($value);
            $half2 = "{\n$spaces      $values  $spaces}\n";
        }
    }
    return $half1 . $half2;
}


function buildLinePlain($changeType, $property, $value1 = '', $value2 = '')
{
    $line = '';

    if ($changeType === 'removed') {
        $line = "'$property' was $changeType";
    } elseif ($changeType === 'added') {
        $line = "'$property' was $changeType with value: '$value1'";
    } elseif ($changeType === 'changed') {
        $line = "'$property' was $changeType. From '$value1' to '$value2'";
    }

    return "Property $line\n";
}
