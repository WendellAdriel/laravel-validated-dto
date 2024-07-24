<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Contracts\Support\Arrayable;
use WendellAdriel\ValidatedDTO\Casting\Castable;

class ArrayableObjectCast implements Castable
{
    public function cast(string $property, mixed $value): Arrayable
    {
        return app(ArrayableObject::class);
    }
}
