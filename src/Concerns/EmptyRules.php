<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

trait EmptyRules
{
    public function rules(): array
    {
        return [];
    }
}
