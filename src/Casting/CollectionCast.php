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
        if (is_string($value)) {
            $jsonDecoded = json_decode($value, true);
            if (is_array($jsonDecoded)) {
                $value = $jsonDecoded;
            }
        }

        return Collection::make($value)
            ->map(fn ($item) => is_null($this->type) ? $item : $this->type->cast($property, $item));
    }
}
