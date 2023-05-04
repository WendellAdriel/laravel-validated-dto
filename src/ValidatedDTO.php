<?php

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\Castable;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;
use WendellAdriel\ValidatedDTO\Validations\DtoValidationParser;

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

        $this->data = $data;

        $this->initConfig();

        $this->process();
        $this->afterProcess();
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
        return $this->toJson(true);
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
     * @throws CastTargetException
     * @throws MissingCastTypeException
     */
    private function process(): void
    {
        if (! $this->isValidData()) {
            $this->failedValidation();
        }

        $this->fillValidatedValues();
        $this->fillEmptyValues();
        $this->fillDefaultValues();
    }

    private function afterProcess(): void
    {
        $this->convertValidatedValue();
        $this->fillProperties();
    }

    /**
     * @throws CastTargetException
     * @throws MissingCastTypeException
     */
    private function isValidCastValue(string $key): bool
    {
        /** @var array<Castable> $casts */
        $casts = $this->getImplodedCasts();

        if (! array_key_exists($key, $casts)) {
            if ($this->requireCasting) {
                throw new MissingCastTypeException($key);
            }

            return false;
        }

        if (! ($casts[$key] instanceof Castable)) {
            throw new CastTargetException($key);
        }

        return true;
    }

    /**
     * @throws CastTargetException
     * @throws MissingCastTypeException
     */
    private function fillValidatedValues(): void
    {
        $acceptedKeys = array_keys($this->getImplodedRules());

        /** @var array<Castable> $casts */
        $casts = $this->getImplodedCasts();

        foreach ($this->getImplodedData() as $key => $value) {
            if (! in_array($key, $acceptedKeys)) {
                continue;
            }

            if (! $this->isValidCastValue($key)) {
                $this->validatedData[$key] = $value;

                continue;
            }

            $this->validatedData[$key] = $this->formatValue($key, $value);
        }
    }

    private function fillEmptyValues(): void
    {
        $acceptedKeys = array_keys($this->getImplodedRules());

        foreach ($acceptedKeys as $property) {
            if (
                ! array_key_exists($property, $this->validatedData) &&
                $this->isOptionalProperty($property)
            ) {
                $this->validatedData[$property] = null;
            }
        }
    }

    /**
     * Handles a passed validation attempt.
     *
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    private function fillDefaultValues(): void
    {
        foreach ($this->getImplodedDefaults() as $key => $value) {
            if (
                property_exists($this, $key) &&
                ! empty($this->{$key})
            ) {
                continue;
            }

            if (! $this->isValidCastValue($key)) {
                $this->validatedData[$key] = $value;

                continue;
            }

            $this->validatedData[$key] = $this->formatValue($key, $value);
        }
    }

    private function formatValue(string $key, mixed $value): mixed
    {
        $casts = $this->getImplodedCasts();

        return $this->shouldReturnNull($key, $value)
            ? null
            : $casts[$key]->cast($key, $value);
    }

    private function convertValidatedValue(): void
    {
        $this->validatedData = Arr::undot($this->validatedData);
    }

    private function fillProperties(): void
    {
        foreach ($this->validatedData as $key => $value) {
            $this->{$key} = $value;
        }
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

    private function getImplodedData(): array
    {
        return Arr::dot($this->data);
    }

    private function getImplodedRules(): array
    {
        return (new DtoValidationParser($this->data))
            ->explode(
                DtoValidationParser::filterConditionalRules(
                    $this->rules(),
                    $this->data,
                ),
            )
            ->rules;
    }

    private function getImplodedDefaults(): array
    {
        $defaults = (new DtoValidationParser($this->data))
            ->explode(
                DtoValidationParser::filterConditionalRules(
                    $this->defaults(),
                    $this->data,
                ),
            )
            ->rules;

        if (is_array($defaults)) {
            return Arr::dot($defaults);
        }

        return $defaults;
    }

    private function getImplodedCasts(): array
    {
        $casts = (new DtoValidationParser($this->data))
            ->explode(
                DtoValidationParser::filterConditionalRules(
                    $this->casts(),
                    $this->data,
                ),
            )
            ->rules;

        return array_map(
            static function ($cast) {
                if (! is_array($cast)) {
                    return $cast;
                }

                return $cast[0];
            },
            $casts,
        );
    }

    private function shouldReturnNull(string $key, mixed $value): bool
    {
        return is_null($value) && $this->isOptionalProperty($key);
    }

    private function isOptionalProperty(string $property): bool
    {
        $rules = $this->getImplodedRules();
        $propertyRules = is_array($rules[$property])
            ? $rules[$property]
            : explode('|', $rules[$property]);

        return in_array('optional', $propertyRules) || in_array('nullable', $propertyRules);
    }
}
