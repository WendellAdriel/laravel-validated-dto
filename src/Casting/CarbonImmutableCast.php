<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\CarbonImmutable;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class CarbonImmutableCast implements Castable
{
    /**
     * @param  string|null  $timezone
     */
    public function __construct(private ?string $timezone = null)
    {
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
            return new CarbonImmutable($value, $this->timezone);
        } catch (Throwable) {
            throw new CastException($property);
        }
    }
}
