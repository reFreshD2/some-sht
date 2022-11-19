<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

class LeaveAction implements ActionInterface
{
    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null)
    {
        if (!$chat) {
            fwrite($channel, "U can't leave from nowhere" . PHP_EOL);
            return null;
        }

        fwrite($channel, 'U leave from chat - ' . $chat . PHP_EOL);
        return null;
    }

    public function isModifyUser(): bool
    {
        return false;
    }

    public function isModifyChat(): bool
    {
        return true;
    }
}
