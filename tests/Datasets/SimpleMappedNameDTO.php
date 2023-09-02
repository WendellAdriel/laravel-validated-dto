<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleMappedNameDTO extends SimpleDTO
{
    public string $first_name;

    public string $last_name;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }

    protected function mapData(): array
    {
        return [
            'first_name' => 'name.first_name',
            'last_name' => 'name.last_name',
        ];
    }
}
