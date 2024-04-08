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
        return config('dto.namespace');
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $fullName = str_replace('\\', '/', "{$this->rootNamespace()}{$name}") . '.php';

        return base_path(lcfirst($fullName));
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath(match (true) {
            $this->option('resource') => 'resource_dto.stub',
            $this->option('simple') => 'simple_dto.stub',
            default => 'dto.stub',
        });
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim("stubs/{$stub}", '/')))
            ? $customPath
            : __DIR__ . '/../stubs/' . $stub;
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
