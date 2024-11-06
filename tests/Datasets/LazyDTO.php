<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class LazyDTO extends ValidatedDTO
{
    public bool $lazyValidation = true;

    public ?string $name;

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
