<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

interface Castable
{
    public function cast(string $property, mixed $value): mixed;
}
