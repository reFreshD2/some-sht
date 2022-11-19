<?php

declare(strict_types=1);

namespace App\Domain\Chat\DTO;

class ChatCommand
{
    public function __construct(
        private string $flag,
        private ?string $message = null,
    ) {
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
