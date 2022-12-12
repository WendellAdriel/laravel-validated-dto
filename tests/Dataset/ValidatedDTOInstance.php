<?php

namespace WendellAdriel\ValidatedDTO\Tests\Dataset;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedDTOInstance extends ValidatedDTO
{
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
}
