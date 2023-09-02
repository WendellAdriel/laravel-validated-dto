<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:dto')]
final class MakeDTOCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:dto';

    /**
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * @var string
     */
    protected $type = 'DTO';

    /**
     * @return string
     */
    protected function rootNamespace()
    {
        return "{$this->laravel->getNamespace()}DTOs\\";
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/DTOs/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function getStub(): string
    {
        return match (true) {
            $this->option('resource') => __DIR__ . '/../stubs/resource_dto.stub',
            $this->option('simple') => __DIR__ . '/../stubs/simple_dto.stub',
            default => __DIR__ . '/../stubs/dto.stub',
        };
    }

    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the DTO already exists'],
            ['simple', null, InputOption::VALUE_NONE, 'If the DTO should be a SimpleDTO'],
            ['resource', null, InputOption::VALUE_NONE, 'If the DTO should be a ResourceDTO'],
        ];
    }
}
