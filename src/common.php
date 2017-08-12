<?php

namespace DiffFinder\common;

function startGenDiff($firstFile, $secondFile)
{
    $firstFileArray = \DiffFinder\parser\fileDataToArray($firstFile);
    $secondFileArray = \DiffFinder\parser\fileDataToArray($secondFile);

    $result = \DiffFinder\diff\findDiff($firstFileArray, $secondFileArray);
    echo $result;
}
