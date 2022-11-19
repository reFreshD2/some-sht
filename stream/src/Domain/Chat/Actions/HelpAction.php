<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Enum\ChatFlag;

class HelpAction implements ActionInterface
{
    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null)
    {
        $helpString = 'Supported command:' . PHP_EOL;
        foreach (ChatFlag::FLAGS_DESCRIPTION as $flag => $description) {
            $helpString .= $flag . ' - ' . $description . PHP_EOL;
        }
        fwrite($channel, $helpString);
    }

    public function isModifyUser(): bool
    {
        return false;
    }

    public function isModifyChat(): bool
    {
        return false;
    }
}
