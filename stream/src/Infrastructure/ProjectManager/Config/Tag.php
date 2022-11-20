<?php

declare(strict_types=1);

namespace App\Infrastructure\ProjectManager\Config;

class Tag
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
