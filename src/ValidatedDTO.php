<?php

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Casting\Castable;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidCastableException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;

abstract class ValidatedDTO
{
    protected array $data = [];

    protected array $validatedData = [];

    private \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator $validator;

    /**
     * @param  array  $data
     *
     * @throws ValidationException|InvalidCastableException
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
        return $this->{$name} ?? null;
    }

    /**
     * Defines the validation rules for the DTO.
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Defines the default values for the properties of the DTO.
     *
     * @return array
     */
    abstract protected function defaults(): array;

    /**
     * Defines the type casting for the properties of the DTO.
     *
     * @return array
     */
    abstract protected function casts(): array;

    /**
     * Creates a DTO instance from a valid JSON string.
     *
     * @param  string  $json
     * @return ValidatedDTO
     *
     * @throws InvalidJsonException|ValidationException|InvalidCastableException
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
     * @throws ValidationException|InvalidCastableException
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
     * @throws ValidationException|InvalidCastableException
     */
    public static function fromModel(Model $model): ValidatedDTO
    {
        return new static($model->toArray());
    }

    /**
     * Creates a DTO instance from the given command arguments.
     *
     * @param  Command  $command
     * @return ValidatedDTO
     *
     * @throws ValidationException|InvalidCastableException
     */
    public static function fromCommandArguments(Command $command): ValidatedDTO
    {
        return new static($command->arguments());
    }

    /**
     * Creates a DTO instance from the given command options.
     *
     * @param  Command  $command
     * @return ValidatedDTO
     *
     * @throws ValidationException|InvalidCastableException
     */
    public static function fromCommandOptions(Command $command): ValidatedDTO
    {
        return new static($command->options());
    }

    /**
     * Creates a DTO instance from the given command arguments and options.
     *
     * @param  Command  $command
     * @return ValidatedDTO
     *
     * @throws ValidationException|InvalidCastableException
     */
    public static function fromCommand(Command $command): ValidatedDTO
    {
        return new static(array_merge($command->arguments(), $command->options()));
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
     *
     * @throws InvalidCastableException
     */
    protected function passedValidation(): void
    {
        $this->validatedData = $this->validatedData();
        /** @var array<Castable> $casts */
        $casts = $this->casts();

        foreach ($this->validatedData as $key => $value) {
            $this->{$key} = $value;
        }

        foreach ($this->defaults() as $key => $value) {
            if (! property_exists($this, $key)) {
                if (! array_key_exists($key, $casts)) {
                    $this->{$key} = $value;
                    $this->validatedData[$key] = $value;
                    continue;
                }

                if (! ($casts[$key] instanceof Castable)) {
                    throw new InvalidCastableException($key);
                }

                $formatted = $casts[$key]->cast($value);
                $this->{$key} = $formatted;
                $this->validatedData[$key] = $formatted;
            }
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
     *
     * @throws InvalidCastableException
     */
    private function validatedData(): array
    {
        $acceptedKeys = array_keys($this->rules());
        $result = [];

        /** @var array<Castable> $casts */
        $casts = $this->casts();

        foreach ($this->data as $key => $value) {
            if (in_array($key, $acceptedKeys)) {
                if (! array_key_exists($key, $casts)) {
                    $result[$key] = $value;
                    continue;
                }

                if (! ($casts[$key] instanceof Castable)) {
                    throw new InvalidCastableException($key);
                }

                $result[$key] = $casts[$key]->cast($value);
            }
        }

        return $result;
    }
}
