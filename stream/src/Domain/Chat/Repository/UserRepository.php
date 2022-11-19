<?php

declare(strict_types=1);

namespace App\Domain\Chat\Repository;

use App\Domain\Chat\Entity\User;
use App\Infrastructure\Persistence\Exception\JsonDBException;
use App\Infrastructure\Persistence\JsonDB;

class UserRepository
{
    private const DB_NAME = 'users';

    public function __construct(
        private JsonDB $DB,
    ) {
    }

    /**
     * @throws JsonDBException
     */
    public function save(User $user): void
    {
        $this->DB->save(self::DB_NAME, $user->toArray());
    }

    /**
     * @throws JsonDBException
     */
    public function isExist(string $userName): bool
    {
        return $this->DB->findOne(self::DB_NAME, ['name' => $userName]) !== null;
    }
}
