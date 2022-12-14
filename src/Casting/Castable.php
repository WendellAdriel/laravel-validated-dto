<?php

namespace WendellAdriel\ValidatedDTO\Casting;

interface Castable
{
    /**
     * Casts the given value.
     *
     * @param  string  $property
     * @param  mixed  $value
     * @return mixed
     */
    public function cast(string $property, mixed $value): mixed;
}
