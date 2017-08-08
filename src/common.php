<?php
namespace Common;

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
        \DiffFinder\findDiff($firstFile, $secondFile);
    }
}
