<?php

namespace WendellAdriel\ValidatedDTO\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:dto')]
class MakeDTOCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DTO';

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return "{$this->laravel->getNamespace()}DTOs\\";
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/DTOs/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->option('simple')
            ? __DIR__.'/../stubs/simple_dto.stub'
            : __DIR__.'/../stubs/dto.stub';
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the DTO already exists'],
            ['simple', null, InputOption::VALUE_NONE, 'If the DTO should be a SimpleDTO instead of a ValidatedDTO'],
        ];
    }
}
