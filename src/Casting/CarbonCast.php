<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\Carbon;

class CarbonCast implements Castable
{
    /**
     * @param  string|null  $timezone
     */
    public function __construct(private ?string $timezone = null)
    {

    }

    /**
     * @param  mixed  $value
     * @return Carbon
     */
    public function cast(mixed $value): Carbon
    {
        return new Carbon($value, $this->timezone);
    }
}