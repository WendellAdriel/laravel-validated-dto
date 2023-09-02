<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Providers;

use Illuminate\Support\ServiceProvider;
use WendellAdriel\ValidatedDTO\Console\Commands\MakeDTOCommand;
use WendellAdriel\ValidatedDTO\Contracts\BaseDTO;

final class ValidatedDTOServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(MakeDTOCommand::class);
        }

        $this->publishes(
            [
                __DIR__ . '/../../config/dto.php' => base_path('config/dto.php'),
            ],
            'config'
        );
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
