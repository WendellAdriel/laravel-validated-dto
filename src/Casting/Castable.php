<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

interface Castable
{
    /**
     * Casts the given value.
     */
    public function cast(string $property, mixed $value): mixed;
}
