<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Contracts\Support\Arrayable;
use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ArrayableObjectDTO extends ValidatedDTO
{
    use EmptyCasts, EmptyDefaults, EmptyRules;

    #[Rules(['required'])]
    #[Cast(type: ArrayableObjectCast::class)]
    public Arrayable $object;
}
