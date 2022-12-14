<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\Carbon;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class CarbonCast implements Castable
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
     * @return Carbon
     *
     * @throws CastException
     */
    public function cast(string $property, mixed $value): Carbon
    {
        try {
            return new Carbon($value, $this->timezone);
        } catch (Throwable) {
            throw new CastException($property);
        }
    }
}
