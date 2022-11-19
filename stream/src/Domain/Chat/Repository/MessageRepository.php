<?php

declare(strict_types=1);

namespace App\Domain\Chat\Repository;

use App\Domain\Chat\Entity\Message;
use App\Infrastructure\Persistence\Exception\JsonDBException;
use App\Infrastructure\Persistence\JsonDB;

class MessageRepository
{
    private const DB_NAME = 'messages';

    public function __construct(
        private JsonDB $DB,
    ) {
    }

    /**
     * @throws JsonDBException
     */
    public function save(Message $message): void
    {
        $this->DB->save(self::DB_NAME, $message->toArray());
    }

    /**
     * @return Message[]
     * @throws JsonDBException
     */
    public function getMessages(string $user1, string $user2): array
    {
        $user1Messages = $this->DB->findAll(self::DB_NAME, [
            'author' => $user1,
            'receiver' => $user2,
        ]);
        $user2Messages = $this->DB->findAll(self::DB_NAME, [
            'author' => $user2,
            'receiver' => $user1,
        ]);

        return array_map(static function (array $messageData) {
            return Message::fromArray($messageData);
        }, array_merge($user1Messages, $user2Messages));
    }
}
