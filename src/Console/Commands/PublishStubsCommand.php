<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'dto:stubs')]
final class PublishStubsCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'dto:stubs';

    /**
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'dto:stubs';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dto:stubs {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all stubs that are available for customization';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'validatedDTO-stubs',
            '--force' => $this->option('force'),
        ]);
    }
}
