<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class NestedCastsDTOInstance extends ValidatedDTO
{
    public array $user;

    public bool $isActive;

    protected function rules(): array
    {
        return [
            'user' => 'required|array',
            'user.name' => 'required|string',
            'user.age' => 'required|integer|gt:18',
            'user.payments' => 'array',
            'user.payments.*' => 'required|integer',
        ];
    }

    protected function defaults(): array
    {
        return [
            'user.payments' => [10],
        ];
    }

    protected function casts(): array
    {
        return [
            'user' => new ArrayCast(),
            'user.name' => new StringCast(),
            'user.age' => new IntegerCast(),
            'user.payments' => new ArrayCast(),
            'user.payments.*' => new IntegerCast(),
        ];
    }
}
