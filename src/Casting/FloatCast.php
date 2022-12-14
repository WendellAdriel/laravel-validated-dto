<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class FloatCast implements Castable
{
    /**
     * @param  string  $property
     * @param  mixed  $value
     * @return float
     *
     * @throws CastException
     */
    public function cast(string $property, mixed $value): float
    {
        if (! is_numeric($value)) {
            throw new CastException($property);
        }

        return (float) $value;
    }
}
