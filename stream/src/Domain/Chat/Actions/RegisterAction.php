<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Entity\User;
use App\Domain\Chat\Repository\UserRepository;
use App\Infrastructure\Persistence\Exception\JsonDBException;

class RegisterAction implements ActionInterface
{
    public function __construct(
        private UserRepository $repository,
    ) {
    }

    public function __invoke($channel, ?string $user = null, ?string $chat = null, ?string $message = null): ?string
    {
        if ($user) {
            fwrite($channel, 'U already register' . PHP_EOL);
            return $user;
        }

        $userName = trim($message);
        if (empty($userName)) {
            fwrite($channel, "Enter username by \r \<username\>" . PHP_EOL);
            return null;
        }

        try {
            $userExist = $this->repository->isExist($userName);
        } catch (JsonDBException $e) {
            fwrite($channel, "Ooops ... {$e->getMessage()}");
            return null;
        }

        if (!$userExist) {
            try {
                $this->repository->save(new User($userName));
            } catch (JsonDBException $e) {
                fwrite($channel, "Ooops ... {$e->getMessage()}");
                return null;
            }
        }

        return $userName;
    }

    public function isModifyUser(): bool
    {
        return true;
    }

    public function isModifyChat(): bool
    {
        return false;
    }
}
