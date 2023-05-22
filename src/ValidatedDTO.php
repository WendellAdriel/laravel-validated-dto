<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Casting\Castable;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

abstract class ValidatedDTO extends SimpleDTO
{
    /**
     * Defines the validation rules for the DTO.
     */
    abstract protected function rules(): array;

    /**
     * Defines the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Defines the custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Builds the validated data from the given data and the rules.
     *
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function validatedData(): array
    {
        $acceptedKeys = array_keys($this->rules());
        $result = [];

        /** @var array<Castable> $casts */
        $casts = $this->casts();

        foreach ($this->data as $key => $value) {
            if (in_array($key, $acceptedKeys)) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }
                    $result[$key] = $value;

                    continue;
                }

                if (! ($casts[$key] instanceof Castable)) {
                    throw new CastTargetException($key);
                }

                $result[$key] = $this->shouldReturnNull($key, $value)
                    ? null
                    : $casts[$key]->cast($key, $value);
            }
        }

        foreach ($acceptedKeys as $property) {
            if (
                ! array_key_exists($property, $result) &&
                $this->isOptionalProperty($property)
            ) {
                $result[$property] = null;
            }
        }

        return $result;
    }

    protected function isValidData(): bool
    {
        $this->validator = Validator::make(
            $this->data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        return $this->validator->passes();
    }

    /**
     * Handles a failed validation attempt.
     *
     *
     * @throws ValidationException
     */
    protected function failedValidation(): void
    {
        throw new ValidationException($this->validator);
    }

    protected function shouldReturnNull(string $key, mixed $value): bool
    {
        return is_null($value) && $this->isOptionalProperty($key);
    }

    private function isOptionalProperty(string $property): bool
    {
        $rules = $this->rules();
        $propertyRules = is_array($rules[$property])
            ? $rules[$property]
            : explode('|', $rules[$property]);

        return in_array('optional', $propertyRules) || in_array('nullable', $propertyRules);
    }
}
