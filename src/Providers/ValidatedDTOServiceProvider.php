<?php

namespace WendellAdriel\ValidatedDTO\Providers;

use Illuminate\Support\ServiceProvider;
use WendellAdriel\ValidatedDTO\Console\Commands\MakeDTOCommand;

class ValidatedDTOServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(MakeDTOCommand::class);
        }
    }

    /**
     * @return void
     */
    public function register()
    {
        //
    }
}
