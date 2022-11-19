<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Entity\Message;
use App\Domain\Chat\Repository\MessageRepository;
use App\Infrastructure\Persistence\Exception\JsonDBException;

class WriteMessageAction implements ActionInterface
{
    public function __construct(
        private MessageRepository $repository,
    ) {
    }

    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null)
    {
        if (!$user) {
            fwrite($channel, 'Register pls!' . PHP_EOL);
            return;
        }
        if (!$chat) {
            fwrite($channel, 'Choose chat!' . PHP_EOL);
            return;
        }
        if (empty($message)) {
            fwrite($channel, 'Write not empty message!' . PHP_EOL);
        }

        try {
            $this->repository->save(new Message(new \DateTimeImmutable(), $user, $chat, $message));
        } catch (JsonDBException $exception) {
            fwrite($channel, "Ooops ... {$exception->getMessage()}");
        }
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
