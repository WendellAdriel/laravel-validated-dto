<?php

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\Castable;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

abstract class ValidatedDTO implements CastsAttributes
{
    protected array $data = [];

    protected array $validatedData = [];

    protected bool $requireCasting = false;

    private \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator $validator;

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public function __construct(?array $data = null)
    {
        if (is_null($data)) {
            return;
        }

        $this->data = $this->parseData($data);

        $this->initConfig();

        $this->isValidData()
            ? $this->passedValidation()
            : $this->failedValidation();
    }

    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->{$name} ?? null;
    }

    /**
     * Defines the validation rules for the DTO.
     */
    abstract protected function rules(): array;

    /**
     * Defines the default values for the properties of the DTO.
     */
    abstract protected function defaults(): array;

    /**
     * Defines the type casting for the properties of the DTO.
     */
    abstract protected function casts(): array;

    protected function mapBeforeValidation(): array
    {
        return [];
    }

    protected function mapBeforeExport(): array
    {
        return [];
    }

    /**
     * Creates a DTO instance from a valid JSON string.
     *
     * @return $this
     *
     * @throws InvalidJsonException|ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromJson(string $json): self
    {
        $jsonDecoded = json_decode($json, true);
        if (! is_array($jsonDecoded)) {
            throw new InvalidJsonException();
        }

        return new static($jsonDecoded);
    }

    /**
     * Creates a DTO instance from Array.
     *
     * @return $this
     *
     * @throws CastTargetException|MissingCastTypeException
     */
    public static function fromArray(array $array): self
    {
        return new static($array);
    }

    /**
     * Creates a DTO instance from a Request.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromRequest(Request $request): self
    {
        return new static($request->all());
    }

    /**
     * Creates a DTO instance from the given model.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromModel(Model $model): self
    {
        return new static($model->toArray());
    }

    /**
     * Creates a DTO instance from the given command arguments.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandArguments(Command $command): self
    {
        return new static($command->arguments());
    }

    /**
     * Creates a DTO instance from the given command options.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandOptions(Command $command): self
    {
        return new static($command->options());
    }

    /**
     * Creates a DTO instance from the given command arguments and options.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommand(Command $command): self
    {
        return new static(array_merge($command->arguments(), $command->options()));
    }

    /**
     * Returns the DTO validated data in array format.
     */
    public function toArray(): array
    {
        return $this->validatedData;
    }

    /**
     * Returns the DTO validated data in a JSON string format.
     */
    public function toJson(bool $pretty = false): string
    {
        return $pretty
            ? json_encode($this->validatedData, JSON_PRETTY_PRINT)
            : json_encode($this->validatedData);
    }

    /**
     * Returns the DTO validated data in a pretty JSON string format.
     */
    public function toPrettyJson(): string
    {
        return json_encode($this->validatedData, JSON_PRETTY_PRINT);
    }

    /**
     * Creates a new model with the DTO validated data.
     */
    public function toModel(string $model): Model
    {
        return new $model($this->validatedData);
    }

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
     * Cast the given value to a DTO instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public function get($model, $key, $value, $attributes)
    {
        $arrayCast = new ArrayCast();

        return new static($arrayCast->cast($key, $value));
    }

    /**
     * Prepare the value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        if ($value instanceof ValidatedDTO) {
            return $value->toJson();
        }

        return '';
    }

    /**
     * Handles a passed validation attempt.
     *
     *
     * @throws MissingCastTypeException|CastTargetException
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
            if (
                ! property_exists($this, $key) ||
                empty($this->{$key})
            ) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }

                    $this->{$key} = $value;
                    $this->validatedData[$key] = $value;

                    continue;
                }

                if (! ($casts[$key] instanceof Castable)) {
                    throw new CastTargetException($key);
                }

                $formatted = $this->shouldReturnNull($key, $value)
                    ? null
                    : $casts[$key]->cast($key, $value);
                $this->{$key} = $formatted;
                $this->validatedData[$key] = $formatted;
            }
        }
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

    /**
     * Inits the configuration for the DTOs.
     */
    private function initConfig(): void
    {
        $config = config('dto');
        if (! empty($config) && array_key_exists('require_casting', $config)) {
            $this->requireCasting = $config['require_casting'];
        }
    }

    /**
     * Checks if the data is valid for the DTO.
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
     *
     * @throws MissingCastTypeException|CastTargetException
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

    private function shouldReturnNull(string $key, mixed $value): bool
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

    private function parseData(?array $data): array
    {
        if (is_null($data)) {
            return [];
        }

        if (empty($this->mapBeforeValidation())) {
            return $data;
        }

        $values = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->mapBeforeValidation())) {
                $values[$this->mapBeforeValidation()[$key]] = $value;
                continue;
            }
            $values[$key] = $value;
        }
        return $values;
    }
}
