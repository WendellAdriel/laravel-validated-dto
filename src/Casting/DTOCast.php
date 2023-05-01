<?php

namespace WendellAdriel\ValidatedDTO\Casting;

use Illuminate\Validation\ValidationException;
use Throwable;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class DTOCast implements Castable
{
    public function __construct(
        private string $dtoClass,
        private array $config = []
    ) {
    }

    /**
     * @throws CastException|CastTargetException|ValidationException
     */
    public function cast(string $property, mixed $value): ValidatedDTO
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        try {
            $dto = new $this->dtoClass($value, $this->config);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new CastException($property);
        }

        if (! ($dto instanceof ValidatedDTO)) {
            throw new CastTargetException($property);
        }

        return $dto;
    }
}
