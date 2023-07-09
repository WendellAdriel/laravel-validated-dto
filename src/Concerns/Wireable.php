<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

use stdClass;

trait Wireable
{
    public static function fromLivewire($value)
    {
        if (is_array($value)) {
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
        return json_decode(json_encode($this), true);
    }
}
