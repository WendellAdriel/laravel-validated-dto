<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleUserDTO extends SimpleDTO
{
    public SimpleNameDTO $name;

    public string $email;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'name' => new DTOCast(SimpleNameDTO::class),
        ];
    }

    protected function mapBeforeExport(): array
    {
        return [
            'name.first_name' => 'first_name',
            'name.last_name' => 'last_name',
        ];
    }
}
