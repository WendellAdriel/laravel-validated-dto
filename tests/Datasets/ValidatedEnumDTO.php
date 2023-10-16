<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\DefaultValue;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\CarbonCast;
use WendellAdriel\ValidatedDTO\Casting\CarbonImmutableCast;
use WendellAdriel\ValidatedDTO\Casting\EnumCast;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedEnumDTO extends ValidatedDTO
{
    use EmptyCasts, EmptyDefaults, EmptyRules;

    #[Rules(['sometimes', 'string'])]
    #[DefaultValue('ONE')]
    #[Cast(EnumCast::class, DummyEnum::class)]
    public DummyEnum $unitEnum;

    #[Rules(['sometimes', 'string'])]
    #[DefaultValue('bar')]
    #[Cast(EnumCast::class, DummyBackedEnum::class)]
    public DummyBackedEnum $backedEnum;

    #[Rules(['sometimes', 'string'])]
    #[DefaultValue('2023-10-16')]
    #[Cast(CarbonCast::class)]
    public Carbon $carbon;

    #[Rules(['sometimes', 'string'])]
    #[DefaultValue('2023-10-15')]
    #[Cast(CarbonImmutableCast::class)]
    public CarbonImmutable $carbonImmutable;
}
