<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Support\Collection;
use WendellAdriel\ValidatedDTO\Casting\CollectionCast;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserNestedCollectionDTO extends ValidatedDTO
{
    public Collection $names;

    public string $email;

    protected function rules(): array
    {
        return [
            'names' => ['required', 'array'],
            'email' => ['required', 'email'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'names' => new CollectionCast(new DTOCast(NameDTO::class)),
        ];
    }
}
