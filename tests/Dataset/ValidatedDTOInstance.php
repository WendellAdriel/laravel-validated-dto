<?php

namespace WendellAdriel\ValidatedDTO\Tests\Dataset;

use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedDTOInstance extends ValidatedDTO
{
    public string $name;

    public ?int $age = null;

    protected function rules(): array
    {
        return [
            'name' => 'required',
            'age' => 'numeric',
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'name' => new StringCast(),
            'age' => new IntegerCast(),
        ];
    }
}
