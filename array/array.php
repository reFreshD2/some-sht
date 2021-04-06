<?php

$myArray = range(0, 20, 2);

function myReverse(array $array): array
{
    $myArray = [];
    end($array);
    for ($i = 0; $i < count($array); $i++) {
        $myArray[key($array)] = current($array);
        prev($array);
    }
    return $myArray;
}

var_dump($myArray);
var_dump(myReverse($myArray));
