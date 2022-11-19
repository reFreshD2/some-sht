<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

interface ActionInterface
{
    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null);
    public function isModifyUser(): bool;
    public function isModifyChat(): bool;
}
