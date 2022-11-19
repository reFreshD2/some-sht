<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Config;

use App\Infrastructure\Persistence\Config\Exception\InvalidConfigException;
use App\Infrastructure\Persistence\Config\Enum\ConfigEnum;

class ConfigValidator
{
    private const TABLE_KEY = 'tables';

    /**
     * @throws InvalidConfigException
     */
    public function validate(array $configData): void
    {
        if (!array_key_exists(ConfigEnum::TABLES_KEY, $configData)) {
            throw new InvalidConfigException('no tables');
        }

        foreach ($configData[ConfigEnum::TABLES_KEY] as $tableColumn) {
            if (!is_array($tableColumn) || count($tableColumn)) {
                throw new InvalidConfigException('empty tables');
            }
        }
    }
}
