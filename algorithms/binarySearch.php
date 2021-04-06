<?php

function ordered(array $array): bool
{
    for ($i = 0; $i < count($array) - 1; $i++) {
        if ($array[$i] > $array[$i + 1]) {
            return false;
        }
    }
    return true;
}

function search(array $array, $haystack): int
{
    if (!ordered($array)) {
        throw new Exception('Не упорядоченный массив');
    }
    $left = 0;
    $right = count($array) - 1;
    while ($left <= $right) {
        $mid = round(($left + $right) / 2, 0, PHP_ROUND_HALF_DOWN);
        if ($array[$mid] === $haystack) {
            return $mid;
        }
        if ($array[$mid] > $haystack) {
            $right = $mid - 1;
        } else {
            $left = $mid + 1;
        }
    }
    return -1;
}

try {
    echo search(range(0, 10,), 9) . PHP_EOL;
    echo search(range(0, 10), 11) . PHP_EOL;
    echo search(array(1, 5, 2, 4, 3), 5) . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage();
}
