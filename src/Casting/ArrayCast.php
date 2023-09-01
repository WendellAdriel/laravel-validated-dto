<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

final class ArrayCast implements Castable
{
    public function cast(string $property, mixed $value): array
    {
        if (is_string($value)) {
            $jsonDecoded = json_decode($value, true);

            return is_array($jsonDecoded) ? $jsonDecoded : [$value];
        }

        return is_array($value) ? $value : [$value];
    }
}
