<?php

namespace App\Infrastructure\Persistence\Config;

use App\Infrastructure\Persistence\Exception\JsonDBException;

class Config
{
    private array $tables;

    public function addTable(string $tableName, array $columns): void
    {
        $this->tables[$tableName] = $columns;
    }

    private function hasTable(string $tableName): bool
    {
        return isset($this->tables[$tableName]);
    }

    /**
     * @throws JsonDBException
     */
    public function getTableColumns(string $tableName): array
    {
        if (!$this->hasTable($tableName)) {
            throw new JsonDBException('undefined table');
        }

        return $this->tables[$tableName];
    }
}
