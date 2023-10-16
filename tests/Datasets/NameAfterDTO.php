<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Validation\Validator;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class NameAfterDTO extends ValidatedDTO
{
    use EmptyCasts, EmptyDefaults, EmptyRules;

    #[Rules(['required', 'string'])]
    public string $first_name;

    #[Rules(['required', 'string'])]
    public string $last_name;

    protected function after(Validator $validator): void
    {
        $validator->errors()->add('test', 'After test!');
    }
}
