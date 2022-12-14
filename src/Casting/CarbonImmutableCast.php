<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\CarbonImmutable;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class CarbonImmutableCast implements Castable
{
    /**
     * @param  string|null  $timezone
     * @param  string|null  $format
     */
    public function __construct(
        private ?string $timezone = null,
        private ?string $format = null
    ) {
    }

    /**
     * @param  string  $property
     * @param  mixed  $value
     * @return CarbonImmutable
     *
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
