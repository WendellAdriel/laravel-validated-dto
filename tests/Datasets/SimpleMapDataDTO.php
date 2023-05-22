<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleMapDataDTO extends SimpleDTO
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

    protected function mapBeforeValidation(): array
    {
        return [
            'full_name' => 'name',
        ];
    }

    protected function mapBeforeExport(): array
    {
        return [
            'name' => 'username',
        ];
    }
}
