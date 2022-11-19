<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Config;

use App\Infrastructure\Persistence\Config\Enum\ConfigEnum;
use App\Infrastructure\Persistence\Config\Exception\InvalidConfigException;

class ConfigBuilder
{
    private ConfigValidator $validator;
    private string $configPath;

    public function __construct(ConfigValidator $validator)
    {
        $this->validator = $validator;
    }

    public function setConfigPath(string $configPath): self
    {
        $this->configPath = $configPath;
        return $this;
    }

    /**
     * @throws InvalidConfigException
     */
    public function build(): Config
    {
        $data = yaml_parse_file($this->configPath);
        $this->validator->validate($data);

        $config = new Config();
        foreach ($data[ConfigEnum::TABLES_KEY] as $tableName => $columns) {
            $config->addTable($tableName, $columns);
        }

        return $config;
    }
}
