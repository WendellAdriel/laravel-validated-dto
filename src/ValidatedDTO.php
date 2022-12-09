<?php

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;

abstract class ValidatedDTO
{
    protected array $data = [];

    protected array $validatedData = [];

    private \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator $validator;

    /**
     * @param  array  $data
     *
     * @throws ValidationException
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->isValidData()
            ? $this->passedValidation()
            : $this->failedValidation();
    }

    /**
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->{$name};
    }

    /**
     * Defines the validation rules for the DTO.
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Creates a DTO instance from a valid JSON string.
     *
     * @param  string  $json
     * @return ValidatedDTO
     *
     * @throws InvalidJsonException|ValidationException
     */
    public static function fromJson(string $json): ValidatedDTO
    {
        $jsonDecoded = json_decode($json, true);
        if (! is_array($jsonDecoded)) {
            throw new InvalidJsonException();
        }

        return new static($jsonDecoded);
    }

    /**
     * Creates a DTO instance from a Request.
     *
     * @param  Request  $request
     * @return ValidatedDTO
     *
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): ValidatedDTO
    {
        return new static($request->all());
    }

    /**
     * Creates a DTO instance from the given model.
     *
     * @param  Model  $model
     * @return ValidatedDTO
     *
     * @throws ValidationException
     */
    public static function fromModel(Model $model): ValidatedDTO
    {
        return new static($model->toArray());
    }

    /**
     * Returns the DTO validated data in array format.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->validatedData;
    }

    /**
     * Returns the DTO validated data in a JSON string format.
     *
     * @param  bool  $pretty
     * @return string
     */
    public function toJson(bool $pretty = false): string
    {
        return $pretty
            ? json_encode($this->validatedData, JSON_PRETTY_PRINT)
            : json_encode($this->validatedData);
    }

    /**
     * Creates a new model with the DTO validated data.
     *
     * @param  string  $model
     * @return Model
     */
    public function toModel(string $model): Model
    {
        return new $model($this->validatedData);
    }

    /**
     * Defines the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Defines the custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Handles a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        $this->validatedData = $this->validatedData();

        foreach ($this->validatedData as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Handles a failed validation attempt.
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(): void
    {
        throw new ValidationException($this->validator);
    }

    /**
     * Checks if the data is valid for the DTO.
     *
     * @return bool
     */
    private function isValidData(): bool
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
     * Builds the validated data from the given data and the rules.
     *
     * @return array
     */
    private function validatedData(): array
    {
        $acceptedKeys = array_keys($this->rules());
        $result = [];

        foreach ($this->data as $key => $value) {
            if (in_array($key, $acceptedKeys)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
