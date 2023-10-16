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

    protected function after(\Illuminate\Validation\Validator $validator): void
    {
        // Do nothing
    }

    /**
     * Builds the validated data from the given data and the rules.
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function validatedData(): array
    {
        $acceptedKeys = array_keys($this->rulesList());
        $result = [];

        /** @var array<Castable> $casts */
        $casts = $this->buildCasts();

        foreach ($this->data as $key => $value) {
            if (in_array($key, $acceptedKeys)) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }
                    $result[$key] = $value;

                    continue;
                }

                $result[$key] = $this->shouldReturnNull($key, $value)
                    ? null
                    : $this->castValue($casts[$key], $key, $value);
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
            $this->rulesList(),
            $this->messagesList(),
            $this->attributes()
        );

        $this->validator->after(fn (\Illuminate\Validation\Validator $validator) => $this->after($validator));

        return $this->validator->passes();
    }

    /**
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
        $rules = $this->rulesList();
        $propertyRules = is_array($rules[$property])
            ? $rules[$property]
            : explode('|', $rules[$property]);

        return in_array('optional', $propertyRules) || in_array('nullable', $propertyRules);
    }

    private function rulesList(): array
    {
        return [
            ...$this->rules(),
            ...$this->dtoRules,
        ];
    }

    private function messagesList(): array
    {
        return [
            ...$this->messages(),
            ...$this->dtoMessages,
        ];
    }
}
