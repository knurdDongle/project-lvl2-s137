<?php

namespace RunDiff;

function startGenDiff($firstFile, $secondFile)
{
    $firstFileArray = \ParseFiles\fileDataToArray($firstFile);
    $secondFileArray = \ParseFiles\fileDataToArray($secondFile);

    $result = \DiffFinder\findDiff($firstFileArray, $secondFileArray);
    echo $result;
}
