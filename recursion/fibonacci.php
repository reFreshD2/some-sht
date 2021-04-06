<?php

function fibonacci(int $n): int {
    return $n < 2 ? $n : fibonacci($n-1) + fibonacci($n-2);
}

echo fibonacci(6);
