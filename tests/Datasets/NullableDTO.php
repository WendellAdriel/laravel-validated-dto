<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class NullableDTO extends ValidatedDTO
{
    public string $name;

    public ?int $age;

    public ?string $address;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'age' => ['optional', 'integer'],
            'address' => ['nullable', 'string'],
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
            'address' => new StringCast(),
        ];
    }
}
