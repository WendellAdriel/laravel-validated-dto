<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

final class IntegerCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, mixed $value): int
    {
        if (! is_numeric($value) && $value !== '') {
            throw new CastException($property);
        }

        return (int) $value;
    }
}
