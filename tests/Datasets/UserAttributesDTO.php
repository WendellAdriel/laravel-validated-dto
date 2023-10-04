<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\DefaultValue;
use WendellAdriel\ValidatedDTO\Attributes\Map;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserAttributesDTO extends ValidatedDTO
{
    use EmptyCasts, EmptyDefaults, EmptyRules;

    #[Rules(['required', 'string', 'min:3', 'max:255'])]
    #[Map(data: 'user_name', transform: 'full_name')]
    public string $name;

    #[Rules(rules: ['required', 'email', 'max:255'], messages: ['email.email' => 'The given email is not a valid email address.'])]
    public string $email;

    #[Rules(['sometimes', 'boolean'])]
    #[DefaultValue(true)]
    public bool $active;
}
