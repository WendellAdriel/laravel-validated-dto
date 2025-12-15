<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Attributes\SkipOnTransform;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

final class SkipDTO extends ValidatedDTO
{
    use EmptyCasts,
        EmptyDefaults,
        EmptyRules;

    #[Rules(['required', 'string'])]
    public string $name;

    #[Cast(IntegerCast::class)]
    #[SkipOnTransform]
    #[Rules(['required', 'integer'])]
    public int $age;
}
