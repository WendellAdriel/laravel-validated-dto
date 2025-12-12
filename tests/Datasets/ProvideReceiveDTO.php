<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use WendellAdriel\ValidatedDTO\Attributes\Provide;
use WendellAdriel\ValidatedDTO\Attributes\Receive;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
use WendellAdriel\ValidatedDTO\Enums\PropertyCase;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

#[Receive(PropertyCase::SnakeCase)]
#[Provide(PropertyCase::PascalCase)]
final class ProvideReceiveDTO extends ValidatedDTO
{
    use EmptyCasts,
        EmptyDefaults,
        EmptyRules;

    #[Rules(['required', 'string'])]
    public string $firstName;

    #[Rules(['required', 'string'])]
    public string $lastName;

    #[Rules(['required', 'integer'])]
    public int $age;
}
