<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class ObjectCast implements Castable
{
    /**
     * @param  string  $property
     * @param  mixed  $value
     * @return object
     *
     * @throws CastException
     */
    public function cast(string $property, mixed $value): object
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        return (object) $value;
    }
}
