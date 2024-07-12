<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rules
{
    public function __construct(
        /**
         * @var array<string>
         */
        public array $rules,
        /**
         * @var array<string, string>
         */
        public array $messages = [],
    ) {}
}
