<?php

declare(strict_types=1);

namespace App\Application\Chat;

use App\Domain\Chat\DTO\ChatCommand;

class CommandReceiver
{
    /**
     * @param resource $channel
     */
    public function getCommand($channel): ChatCommand
    {
        $message = fread($channel, 1024);
        $success = preg_match('/^(?<flag>\![hucmgle])(?<message>.*)/', $message, $command);
        while (!$success) {
            fwrite($channel, '[ERROR] unsupported message' . PHP_EOL);
            $message = fread($channel, 1024);
            $success = preg_match('/^(?<flag>\![hucmgle])(?<message>.*)/', $message, $command);
        }

        return new ChatCommand($command['flag'], $command['message']);
    }
}
