<?php

declare(strict_types=1);

namespace App\Infrastructure\ProjectManager\Config;

class ClassArgument
{
    public function __construct(
        private mixed $value,
        private bool $isClass,
    ) {
    }

    public function isClass(): bool
    {
        return $this->isClass;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
