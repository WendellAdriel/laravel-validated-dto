<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Enums;

use Illuminate\Support\Str;

enum PropertyCase
{
    case SnakeCase;

    case PascalCase;

    public function format(string $value): string
    {
        return match ($this) {
            self::SnakeCase => Str::snake($value),
            self::PascalCase => Str::pascal($value),
        };
    }
}
