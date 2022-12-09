<?php

namespace WendellAdriel\ValidatedDTO\Facades;

use Illuminate\Support\Facades\Facade;

class ValidatedDTO extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-validated-dto';
    }
}
