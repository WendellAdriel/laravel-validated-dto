<?php

namespace WendellAdriel\ValidatedDTO\Casting;

interface Castable
{
    /**
     * Casts the given value.
     */
    public function cast(string $property, mixed $value): mixed;
}
