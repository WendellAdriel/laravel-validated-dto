<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleMapBeforeValidationDTO extends SimpleDTO
{
    public string $name;

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
            'full_name' => 'name',
        ];
    }
}
