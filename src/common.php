<?php

namespace RunDiff;

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

        $firstFileArray = \ParseFiles\fileDataToArray($firstFile);
        $secondFileArray = \ParseFiles\fileDataToArray($secondFile);

        $result = \DiffFinder\findDiff($firstFileArray, $secondFileArray);
        echo $result;
    }
}
