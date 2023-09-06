<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

enum DummyBackedEnum: string
{
    case FOO = 'foo';

    case BAR = 'bar';

    case BAZ = 'baz';
}
