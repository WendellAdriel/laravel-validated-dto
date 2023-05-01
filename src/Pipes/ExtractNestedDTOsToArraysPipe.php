<?php

namespace WendellAdriel\ValidatedDTO\Pipes;

use Closure;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ExtractNestedDTOsToArraysPipe
{
    public function handle(array $array, Closure $next)
    {
        foreach ($array as $key => $value) {
            if ($value instanceof ValidatedDTO) {
                $array[$key] = $value->toArray();
            }
        }

        return $next($array);
    }
}
