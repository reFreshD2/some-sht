<?php

declare(strict_types=1);

namespace App\Domain\Chat\Entity;

class User
{
    public function __construct(
        private string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
