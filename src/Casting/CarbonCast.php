<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use Carbon\Carbon;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

final class CarbonCast implements Castable
{
    public function __construct(
        private ?string $timezone = null,
        private ?string $format = null
    ) {}

    /**
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
