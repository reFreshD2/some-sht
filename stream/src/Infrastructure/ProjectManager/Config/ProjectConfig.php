<?php

namespace App\Infrastructure\ProjectManager\Config;

use App\Infrastructure\ProjectManager\Exception\InvalidProjectConfigurationException;

class ProjectConfig
{
    /**
     * @var ProjectConfigItem[]
     */
    private array $projectConfigItems;

    /**
     * @throws InvalidProjectConfigurationException
     */
    public function addItem(ProjectConfigItem $item): void
    {
        if (isset($this->projectConfigItems[$item->getClass()])) {
            throw new InvalidProjectConfigurationException("duplicate {$item->getClass()} definition");
        }
        $this->projectConfigItems[$item->getClass()] = $item;
    }

    /**
     * @throws InvalidProjectConfigurationException
     */
    public function getItem(string $class): ProjectConfigItem
    {
        if (!isset($this->projectConfigItems[$class])) {
            throw new InvalidProjectConfigurationException("$class undefined");
        }

        return $this->projectConfigItems[$class];
    }
}
