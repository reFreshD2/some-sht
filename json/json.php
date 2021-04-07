<?php

$myArray = range(0, 20, 2);

class A
{
    public string $a;
    private string $b;
    protected string $c;

    public function __construct(string $a, string $b, string $c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
}

$myA = new A("a", "b", "c");
$newArray = ["a" => "a", ["b" => "b"]];

var_dump(json_encode($myA));
var_dump(json_encode($myArray));
var_dump(json_encode($newArray));

$stringToDecode = "{\"a\": 1, \"b\": \"b\", \"c\": {\"a\": 2, \"b\": \"a\"}}";

var_dump(json_decode($stringToDecode));
var_dump(json_decode($stringToDecode, true));
