<?php

class A {
    private $a;
    public $b;
    protected $c;

    public function __construct($a,$b,$c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return mixed
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return mixed
     */
    public function getC()
    {
        return $this->c;
    }

    public function __serialize(): array
    {
        return ['a' => $this->a, 'b' => $this->b, 'c' => $this->c];
    }
}

class B {
    private $a;

    public function __construct($a)
    {
        $this->a = $a;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    public function __sleep(): array
    {
        return ['a'];
    }
}

$object = new A(1,2,3);
$serialize = serialize($object);
var_dump($serialize);
$newObject = unserialize($serialize);
echo $newObject->getA() . PHP_EOL;
echo $newObject->getB() . PHP_EOL;
echo $newObject->getC() . PHP_EOL;

$objectB = new B(1);
$serializeB = serialize($objectB);
var_dump($serializeB);
$newObjectB = unserialize($serializeB);
echo $newObjectB->getA();
