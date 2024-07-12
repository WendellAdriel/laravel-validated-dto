<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Cast
{
    public function __construct(
        /**
         * @var class-string
         */
        public string $type,
        /**
         * @var class-string
         */
        public ?string $param = null,
    ) {}
}
