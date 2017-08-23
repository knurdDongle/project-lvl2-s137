<?php

namespace DiffFinder\common;

function startGenDiff($file1, $file2, $format)
{
    $file1Extension = pathinfo($file1, PATHINFO_EXTENSION);
    $file2Extension = pathinfo($file2, PATHINFO_EXTENSION);

    $fileArray1 = \DiffFinder\parser\dataToArray(file_get_contents($file1), $file1Extension);
    $fileArray2 = \DiffFinder\parser\dataToArray(file_get_contents($file2), $file2Extension);

    $result = \DiffFinder\diff\findDiff($fileArray1, $fileArray2, $format);

    echo $result;
}
