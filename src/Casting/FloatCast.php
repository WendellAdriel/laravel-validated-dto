<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class FloatCast implements Castable
{
    /**
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
