<?php

class SellDTO
{
    private array $properties;
    private int $price;
    private string $title;

    public function __construct(string $title, int $price, array $properties)
    {
        $this->properties = $properties;
        $this->price = $price;
        $this->title = $title;
    }
}
