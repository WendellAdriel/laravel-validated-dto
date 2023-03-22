<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class AttributesDTO extends ValidatedDTO
{
    public int $age;

    public string $doc;

    protected function rules(): array
    {
        return [
            'age' => ['required', 'integer'],
            'doc' => ['required', 'string'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'age' => new IntegerCast(),
            'doc' => new StringCast(),
        ];
    }
}
