<?php

namespace ParseFiles;

use Symfony\Component\Yaml\Yaml;

function fileDataToArray($file)
{
    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

    if ($fileExtension === 'json') {
        return json_decode(file_get_contents($file), true);
    } elseif ($fileExtension === 'yml') {
        return yamlParse($file);
    }
}


function yamlParse($file)
{
    return Yaml::parse(file_get_contents($file));
}
