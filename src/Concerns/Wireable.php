<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

use stdClass;

trait Wireable
{
    public static function fromLivewire($value)
    {
        if (is_array($value)) {
            json_decode(json_encode($value), true);

            return new static($value);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return new static($value->toArray());
        }

        if ($value instanceof stdClass) {
            return new static((array) $value);
        }

        return new static([]);
    }

    public function toLivewire(): array
    {
        $fullArray = json_decode(json_encode($this->toArray()), true);

        $filteredArray = array_filter($fullArray, fn ($value) => ! is_null($value));

        return $filteredArray;
    }
}
