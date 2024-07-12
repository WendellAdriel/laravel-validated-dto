<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\CarbonImmutable;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

final class CarbonImmutableCast implements Castable
{
    public function __construct(
        private ?string $timezone = null,
        private ?string $format = null
    ) {}

    /**
     * @throws CastException
     */
    public function cast(string $property, mixed $value): CarbonImmutable
    {
        try {
            return is_null($this->format)
                ? CarbonImmutable::parse($value, $this->timezone)
                : CarbonImmutable::createFromFormat($this->format, $value, $this->timezone);
        } catch (Throwable) {
            throw new CastException($property);
        }
    }
}
