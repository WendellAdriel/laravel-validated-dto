<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Attributes;

use Attribute;
use WendellAdriel\ValidatedDTO\Enums\PropertyCase;

#[Attribute(Attribute::TARGET_CLASS)]
final class Receive
{
    public function __construct(public PropertyCase $propertyCase) {}
}
