<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Support\Collection;
use WendellAdriel\ValidatedDTO\Casting\CollectionCast;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\Concerns\Wireable;
use WendellAdriel\ValidatedDTO\SimpleDTO;

class WireableDTO extends SimpleDTO
{
    use Wireable;

    public ?string $name;

    public ?int $age;

    public ?SimpleNameDTO $simple_name_dto;

    public ?Collection $simple_names_collection;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'name' => new StringCast(),
            'age' => new IntegerCast(),
            'simple_name_dto' => new DTOCast(SimpleNameDTO::class),
            'simple_names_collection' => new CollectionCast(new DTOCast(SimpleNameDTO::class)),
        ];
    }
}
