<?php

declare(strict_types=1);

namespace App\Infrastructure\ProjectManager\Config;

use App\Infrastructure\ProjectManager\Exception\InvalidProjectConfigurationException;
use DirectoryIterator;

class ConfigLoader
{
    private const SUPPORTED_EXTENSION = 'yaml';

    private array $envValues;

    /**
     * @throws InvalidProjectConfigurationException
     */
    public function loadConfigs(string $configPath): ProjectConfig
    {
        $config = new ProjectConfig();
        $this->getEnv();

        $directoryIterator = new DirectoryIterator($configPath);
        foreach ($directoryIterator as $item) {
            if (!$item->isFile()) {
                continue;
            }

            if ($item->getExtension() !== self::SUPPORTED_EXTENSION) {
                throw new InvalidProjectConfigurationException('invalid config format ' . $item->getExtension());
            }

            foreach ($this->getItemsFromFile($item->getPathname()) as $configItem) {
                $config->addItem($configItem);
            }
        }

        return $config;
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    private function getItemsFromFile(string $filePath): array
    {
        $ndocs = 0;
        $data = \yaml_parse_file($filePath, 0, $ndocs, [
            '!env' => [$this, 'loadEnvValue'],
        ]);

        if (!isset($data['services'])) {
            throw new InvalidProjectConfigurationException("Config $filePath doesn't contains service definition");
        }

        $configItems = [];
        foreach ($data['services'] as $class => $arguments) {
            $configItems[] = new ProjectConfigItem($class, array_map(static function ($argument) {
                if (is_string($argument) && str_contains($argument, '@')) {
                    return new ClassArgument(substr($argument, 1), true);
                }
                return new ClassArgument($argument, false);
            }, $arguments));
        }

        return $configItems;
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    private function loadEnvValue($value, $tag, $flags): mixed
    {
        if (!isset($this->envValues[$value])) {
            throw new InvalidProjectConfigurationException('Undefined env ' . $value);
        }

        return $this->envValues[$value];
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    private function getEnv(): void
    {
        $env = fopen($_SERVER['PWD'] . '/.env', 'rb');
        if (!$env) {
            throw new InvalidProjectConfigurationException('Cant open env file');
        }

        while ($str = fgets($env)) {
            $posEq = strpos($str, '=');
            $this->envValues[substr($str, 0, $posEq)] = trim(substr($str, $posEq + 1));
        }
        fclose($env);
    }
}
