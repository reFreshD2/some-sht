<?php

class SellDTO
{
    private array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function getPropertyValue(string $name)
    {
        return $this->properties[$name] ?? null;
    }
}
