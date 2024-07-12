<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use Illuminate\Validation\ValidationException;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\SimpleDTO;

final class DTOCast implements Castable
{
    public function __construct(private string $dtoClass) {}

    /**
     * @throws CastException|CastTargetException|ValidationException
     */
    public function cast(string $property, mixed $value): SimpleDTO
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        try {
            $dto = new $this->dtoClass($value);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new CastException($property);
        }

        if (! ($dto instanceof SimpleDTO)) {
            throw new CastTargetException($property);
        }

        return $dto;
    }
}
