<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\DefaultValue;
use WendellAdriel\ValidatedDTO\Attributes\Map;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\BooleanCast;
use WendellAdriel\ValidatedDTO\Casting\FloatCast;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
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
    #[Cast(BooleanCast::class)]
    public bool $active;

    #[Rules(['sometimes', 'integer'])]
    #[Cast(IntegerCast::class)]
    public ?int $age;

    #[Rules(['sometimes', 'array'])]
    #[Cast(type: ArrayCast::class, param: FloatCast::class)]
    public ?array $grades;
}
