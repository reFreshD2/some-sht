<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Entity\Message;
use App\Domain\Chat\Repository\MessageRepository;
use App\Infrastructure\Persistence\Exception\JsonDBException;
use ArrayIterator;

class GetMessagesAction implements ActionInterface
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

        try {
            $messages = $this->repository->getMessages($user, $chat);
        } catch (JsonDBException $e) {
            fwrite($channel, "Ooops ... {$e->getMessage()}");
            return;
        }

        $messageIterator = new ArrayIterator($messages);
        $messageIterator->uasort(function (Message $a, Message $b) {
            return $a->getDate() <=> $b->getDate();
        });

        foreach ($messageIterator as $message) {
            /**
             * @var Message $message
             */
            fwrite(
                $channel,
                sprintf(
                    "[%s] %s \t [%s]" . PHP_EOL,
                    trim($message->getAuthor()),
                    trim($message->getText()),
                    $message->getDate()->format("d M y H:i:s")
                )
            );
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
