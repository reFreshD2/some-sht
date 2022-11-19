<?php

declare(strict_types=1);

namespace App\Domain\Chat\Entity;

use DateTimeImmutable;

class Message
{
    public function __construct(
        private DateTimeImmutable $date,
        private string $author,
        private string $receiver,
        private string $text,
    ) {
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->getTimestamp(),
            'author' => $this->author,
            'receiver' => $this->receiver,
            'text' => $this->text,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new Message(
            (new DateTimeImmutable())->setTimestamp($data['date']),
            $data['author'],
            $data['receiver'],
            $data['text'],
        );
    }
}
