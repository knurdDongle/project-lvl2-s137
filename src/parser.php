<?php

namespace DiffFinder\parser;

use Symfony\Component\Yaml\Yaml;

function dataToArray($data, $dataFormat)
{
    if ($dataFormat === 'json') {
        return json_decode($data, true);
    } elseif ($dataFormat === 'yml') {
        return Yaml::parse($data);
    }
}
