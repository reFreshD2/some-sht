<?php

declare(strict_types=1);

namespace App\Infrastructure\ProjectManager;

use App\Infrastructure\ProjectManager\Config\ConfigLoader;
use App\Infrastructure\ProjectManager\Config\ProjectConfig;
use App\Infrastructure\ProjectManager\Exception\InvalidProjectConfigurationException;

class ProjectManager
{
    private array $container;
    private ProjectConfig $config;

    /**
     * @throws InvalidProjectConfigurationException
     */
    public function __construct(
        string $configDir,
    ) {
        $this->config = (new ConfigLoader())->loadConfigs($configDir);
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    public function get(string $class): object
    {
        if (!isset($this->container[$class])) {
            $this->init($class);
        }

        return $this->container[$class];
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    private function init(string $class): void
    {
        $classConfig = $this->config->getItem($class);
        $arguments = [];
        foreach ($classConfig->getArguments() as $argument) {
            if ($argument->isClass()) {
                $arguments[] = $this->get($argument->getValue());
            } else {
                $arguments[] = $argument->getValue();
            }
        }
        $this->container[$class] = new $class(...$arguments);
    }
}
