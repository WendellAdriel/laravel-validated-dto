<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\CarbonImmutable;

class CarbonImmutableCast implements Castable
{
    /**
     * @param  string|null  $timezone
     */
    public function __construct(private ?string $timezone = null)
    {

    }

    /**
     * @param  mixed  $value
     * @return CarbonImmutable
     */
    public function cast(mixed $value): CarbonImmutable
    {
        return new CarbonImmutable($value, $this->timezone);
    }
}