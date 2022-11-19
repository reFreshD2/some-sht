<?php

declare(strict_types=1);

namespace App\Infrastructure\ProjectManager\Config;

class ProjectConfigItem
{
    public function __construct(
        private string $class,
        /**
         * @var ClassArgument[] $arguments
         */
        private array $arguments,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return ClassArgument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
