<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleOuterDTO extends SimpleDTO
{
    public string $name;

    public SimpleInnerDTO $inner;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'name' => new StringCast(),
            'number' => new IntegerCast(),
            'inner' => new DTOCast(SimpleInnerDTO::class),
        ];
    }
}
