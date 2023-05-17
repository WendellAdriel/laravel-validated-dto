<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleNameDTO extends SimpleDTO
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
}
