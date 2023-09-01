<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

final class ObjectCast implements Castable
{
    /**
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
