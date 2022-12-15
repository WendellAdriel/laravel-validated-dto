<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Illuminate\Support\Collection;

class CollectionCast implements Castable
{
    /**
     * @param  Castable|null  $type
     */
    public function __construct(private ?Castable $type = null)
    {
    }

    /**
     * @param  string  $property
     * @param  mixed  $value
     * @return Collection
     */
    public function cast(string $property, mixed $value): Collection
    {
        $arrayCast = new ArrayCast();
        $value = $arrayCast->cast($property, $value);

        return Collection::make($value)
            ->when($this->type, function ($collection, $castable) use ($property) {
                return $collection->map(fn ($item) => $castable->cast($property, $item));
            });
    }
}
