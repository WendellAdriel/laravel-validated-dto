<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

trait EmptyDefaults
{
    public function defaults(): array
    {
        return [];
    }
}
