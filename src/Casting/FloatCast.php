<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

final class FloatCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, mixed $value): float
    {
        if (! is_numeric($value) && $value !== '') {
            throw new CastException($property);
        }

        return (float) $value;
    }
}
