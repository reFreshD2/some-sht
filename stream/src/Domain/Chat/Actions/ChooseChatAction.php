<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Repository\UserRepository;
use App\Infrastructure\Persistence\Exception\JsonDBException;

class ChooseChatAction implements ActionInterface
{
    public function __construct(
        private UserRepository $repository,
    ) {
    }

    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null)
    {
        if (!$user) {
            fwrite($channel, 'Register pls!' . PHP_EOL);
            return null;
        }

        $userTo = trim($message);
        if (empty($userTo)) {
            fwrite($channel, "Choose user by \c \<username\>" . PHP_EOL);
            return null;
        }

        try {
            $isExist = $this->repository->isExist($userTo);
        } catch (JsonDBException $e) {
            fwrite($channel, "Ooops ... {$e->getMessage()}");
            return null;
        }

        if ($isExist) {
            fwrite($channel, 'Start chat with user - ' . $userTo . PHP_EOL);
            return $userTo;
        }

        fwrite($channel, "User doesn't exists" . PHP_EOL);
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
