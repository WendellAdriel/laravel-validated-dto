<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\SimpleDTO;

class CallableCastingDTOInstance extends SimpleDTO
{
    #[Cast(DTOCast::class, SimpleNameDTO::class)]
    public SimpleNameDTO $name;

    public ?int $age = null;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'age' => function (string $property, mixed $value) {
                if (! is_numeric($value)) {
                    throw new CastException($property);
                }

                return (int) $value;
            },
        ];
    }
}
