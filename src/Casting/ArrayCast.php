<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

final class ArrayCast implements Castable
{
    public function __construct(private ?Castable $type = null) {}

    public function cast(string $property, mixed $value): array
    {
        if (is_string($value)) {
            $jsonDecoded = json_decode($value, true);

            return is_array($jsonDecoded) ? $jsonDecoded : [$value];
        }

        $result = is_array($value) ? $value : [$value];

        return blank($this->type)
            ? $result
            : array_map(fn ($item) => $this->type->cast($property, $item), $result);
    }
}
