<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use Illuminate\Support\Collection;

final class CollectionCast implements Castable
{
    public function __construct(private ?Castable $type = null) {}

    public function cast(string $property, mixed $value): Collection
    {
        $arrayCast = new ArrayCast();
        $value = $arrayCast->cast($property, $value);

        return Collection::make($value)
            ->when($this->type, fn ($collection, $castable) => $collection->map(fn ($item) => $castable->cast($property, $item)));
    }
}
