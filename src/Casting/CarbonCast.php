<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\Carbon;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class CarbonCast implements Castable
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
     * @return Carbon
     *
     * @throws CastException
     */
    public function cast(string $property, mixed $value): Carbon
    {
        try {
            return is_null($this->format)
                ? Carbon::parse($value, $this->timezone)
                : Carbon::createFromFormat($this->format, $value, $this->timezone);
        } catch (Throwable) {
            throw new CastException($property);
        }
    }
}
