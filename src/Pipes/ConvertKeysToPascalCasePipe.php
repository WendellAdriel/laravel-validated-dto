<?php

namespace WendellAdriel\ValidatedDTO\Pipes;

use Closure;
use Illuminate\Support\Str;

class ConvertKeysToPascalCasePipe
{
    public function handle(array $array, Closure $next)
    {
        foreach ($array as $key => $value) {
            if ($key == ($camelCaseKey = ucfirst(Str::camel($key)))) {
                continue;
            }

            $array[$camelCaseKey] = $value;
            unset($array[$key]);
        }

        return $next($array);
    }
}
