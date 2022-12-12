<?php

namespace WendellAdriel\ValidatedDTO\Casting;

interface Castable
{
    /**
     * Casts the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function cast(mixed $value): mixed;
}