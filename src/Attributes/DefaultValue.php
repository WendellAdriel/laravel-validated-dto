<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue
{
    public function __construct(
        public mixed $value,
    ) {}
}
