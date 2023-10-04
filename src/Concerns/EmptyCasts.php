<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

trait EmptyCasts
{
    public function casts(): array
    {
        return [];
    }
}
