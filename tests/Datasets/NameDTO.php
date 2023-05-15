<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

class NameDTO extends \WendellAdriel\ValidatedDTO\ValidatedDTO
{
    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}
