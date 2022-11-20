<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Persistence\Config\Config;
use App\Infrastructure\Persistence\Config\ConfigBuilder;
use App\Infrastructure\Persistence\Config\Exception\InvalidConfigException;
use App\Infrastructure\Persistence\Exception\JsonDBException;
use JsonException;

class JsonDB
{
    private Config $config;

    /**
     * @throws InvalidConfigException
     */
    public function __construct(
        ConfigBuilder $builder,
        private string $dbPath,
        string $configPath,
    ) {
        $this->config = $builder->setConfigPath($configPath)
            ->build();
        $this->dbPath = $_SERVER['PWD'] . '/' . $this->dbPath;
    }

    /**
     * @throws JsonDBException
     */
    public function save(string $tableName, array $data): void
    {
        $tableColumns = $this->config->getTableColumns($tableName);
        foreach ($data as $key => $value) {
            if (!in_array($key, $tableColumns, true)) {
                throw new JsonDBException('undefined column');
            }
        }

        try {
            $tableData = $this->getTableData($tableName);
        } catch (JsonException $e) {
            throw new JsonDBException('invalid table content', 0, $e);
        }

        $tableData[] = $data;
        $this->write($tableName, $tableData);
    }

    /**
     * @throws JsonDBException
     */
    public function findAll(string $tableName, array $spec): array
    {
        try {
            $tableData = $this->getTableData($tableName);
        } catch (JsonException $e) {
            throw new JsonDBException('invalid table content', 0, $e);
        }

        return array_values(array_filter($tableData, function (array $item) use ($spec) {
            return $this->match($item, $spec);
        }));
    }

    /**
     * @throws JsonDBException
     */
    public function findOne(string $tableName, array $spec): ?array
    {
        try {
            $tableData = $this->getTableData($tableName);
        } catch (JsonException $e) {
            throw new JsonDBException('invalid table content', 0, $e);
        }

        foreach ($tableData as $item) {
            if (!$this->match($item, $spec)) {
                continue;
            }
            return $item;
        }

        return null;
    }

    private function match(array $item, array $spec): bool
    {
        foreach ($spec as $key => $value) {
            if (!array_key_exists($key, $item)) {
                return false;
            }

            if ($item[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws JsonException
     */
    private function getTableData(string $table): array
    {
        $content = file_get_contents($this->dbPath . '/' . $table . '.json');
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonDBException
     */
    private function write(string $tableName, array $tableData): void
    {
        try {
            $content = json_encode($tableData, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonDBException('invalid json data');
        }

        if (!file_put_contents($this->dbPath . '/' . $tableName . '.json', $content)) {
            throw new JsonDBException("didn't save");
        }
    }
}
