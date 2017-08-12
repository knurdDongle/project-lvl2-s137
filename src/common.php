<?php

namespace Common;

use Symfony\Component\Yaml\Yaml;

const DOC = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]
DOC;

function startGenDiff()
{
    $args = \Docopt::handle(DOC);

//    foreach ($args as $k=>$v)
//        echo $k.': '.json_encode($v).PHP_EOL;

    if ($args['<firstFile>']) {
        $firstFile = $args['<firstFile>'];
        $secondFile = $args['<secondFile>'];

        $firstFileArray = fileDataToArray($firstFile);
        $secondFileArray = fileDataToArray($secondFile);

        $result = \DiffFinder\findDiff($firstFileArray, $secondFileArray);
        echo $result;
    }
}


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
