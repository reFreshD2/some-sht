<?php

const OPEN_BRACKET = array('(','{','[');
const CLOSE_BRACKET = array(')','}',']');
const MAP = [
    ')' => '(',
    '}' => '{',
    ']' => '['
];

function validate(string $input):bool {
    $stack = new SplStack();
    for ($i = 0; $i < strlen($input); $i++) {
        if (!in_array($input[$i], array_merge(OPEN_BRACKET, CLOSE_BRACKET), true)) {
            continue;
        }
        if (in_array($input[$i], OPEN_BRACKET, true)) {
            $stack->push($input[$i]);
        } else {
            $buff = new SplStack();
            $reverse = MAP[$input[$i]];
            $isFound = false;
            while (!$isFound) {
                if ($stack->isEmpty()) {
                    return false;
                }
                $top = $stack->pop();
                if ($top === $reverse) {
                    $isFound = true;
                    continue;
                }
                $buff->push($top);
            }
            while (!$buff->isEmpty()) {
                $stack->push($buff->pop());
            }
        }
    }
    return $stack->isEmpty();
}

$testCase = [
    '(())' => true,
    '{{}}' => true,
    '[[]]' => true,
    '({[]})' => true,
    '[{(}])' => true,
    'asf[adf (f df] fd) fdsf {f df}' => true,
    '{)' => false,
    '(()' => false,
    'afaaf(fadfasddg sdsg  fdf adf' => false,
    ')(' => false
];

$ok = 0;
$notOk = 0;
foreach ($testCase as $input => $expected) {
    if (validate($input) === $expected) {
        $ok++;
    } else {
        $notOk++;
    }
}
echo "$ok tests pass. $notOk tests failed.";
