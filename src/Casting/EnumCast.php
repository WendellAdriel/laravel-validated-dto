<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use BackedEnum;
use UnitEnum;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;

final class EnumCast implements Castable
{
    /**
     * @param  class-string<UnitEnum|BackedEnum>  $enum
     */
    public function __construct(protected string $enum) {}

    /**
     * @throws CastException|CastTargetException
     */
    public function cast(string $property, mixed $value): UnitEnum|BackedEnum
    {
        if (! (is_subclass_of($this->enum, UnitEnum::class))) {
            throw new CastTargetException($property);
        }

        if ($value instanceof $this->enum) {
            return $value;
        }

        if (is_subclass_of($this->enum, BackedEnum::class)) {
            if (! is_string($value) && ! is_int($value)) {
                throw new CastException($property);
            }

            $enumCases = array_map(
                fn ($case) => $case->value,
                $this->enum::cases()
            );

            if (! in_array($value, $enumCases)) {
                throw new CastException($property);
            }

            return $this->enum::from($value);
        }

        $enumCases = array_map(
            fn ($case) => $case->name,
            $this->enum::cases()
        );

        if (! in_array($value, $enumCases)) {
            throw new CastException($property);
        }

        $value = constant("{$this->enum}::{$value}");
        if (! $value instanceof $this->enum) {
            throw new CastException($property);
        }

        return $value;
    }
}
