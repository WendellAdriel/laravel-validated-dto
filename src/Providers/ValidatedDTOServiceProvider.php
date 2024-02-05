<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Providers;

use Illuminate\Support\ServiceProvider;
use WendellAdriel\ValidatedDTO\Console\Commands\MakeDTOCommand;
use WendellAdriel\ValidatedDTO\Console\Commands\PublishStubsCommand;
use WendellAdriel\ValidatedDTO\Contracts\BaseDTO;

final class ValidatedDTOServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeDTOCommand::class,
                PublishStubsCommand::class,
            ]);
        }

        $this->publishes(
            [
                __DIR__ . '/../../config/dto.php' => base_path('config/dto.php'),
            ],
            'config'
        );

        $this->publishes([
            __DIR__ . '/../../src/Console/stubs/resource_dto.stub' => base_path('stubs/resource_dto.stub'),
            __DIR__ . '/../../src/Console/stubs/simple_dto.stub' => base_path('stubs/simple_dto.stub'),
            __DIR__ . '/../../src/Console/stubs/dto.stub' => base_path('stubs/dto.stub'),
        ], 'validatedDTO-stubs');
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/dto.php', 'dto');

        $this->app->beforeResolving(BaseDTO::class, function ($class, $parameters, $app) {
            if ($app->has($class)) {
                return;
            }

            $app->bind(
                $class,
                fn ($container) => $class::fromRequest($container['request'])
            );
        });
    }
}
