<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\Lazy;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

#[Lazy]
class LazyAttributeDTO extends ValidatedDTO
{
    use EmptyDefaults;

    public ?string $name;

    public ?int $age = null;

    protected function rules(): array
    {
        return [
            'name' => 'required',
            'age' => 'numeric',
        ];
    }

    protected function casts(): array
    {
        return [
            'name' => new StringCast(),
            'age' => new IntegerCast(),
        ];
    }
}
