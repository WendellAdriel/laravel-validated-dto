<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO;

use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use JsonSerializable;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use UnitEnum;
use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\DefaultValue;
use WendellAdriel\ValidatedDTO\Attributes\Lazy;
use WendellAdriel\ValidatedDTO\Attributes\Map;
use WendellAdriel\ValidatedDTO\Attributes\Provide;
use WendellAdriel\ValidatedDTO\Attributes\Receive;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\Castable;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Casting\EnumCast;
use WendellAdriel\ValidatedDTO\Concerns\DataResolver;
use WendellAdriel\ValidatedDTO\Concerns\DataTransformer;
use WendellAdriel\ValidatedDTO\Contracts\BaseDTO;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

abstract class SimpleDTO implements BaseDTO, CastsAttributes, JsonSerializable
{
    use DataResolver, DataTransformer;

    public bool $lazyValidation = false;

    /** @internal */
    protected array $dtoData = [];

    /** @internal */
    protected array $validatedData = [];

    /** @internal */
    protected bool $requireCasting = false;

    /** @internal */
    protected \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator $validator;

    /** @internal */
    protected array $dtoRules = [];

    /** @internal */
    protected array $dtoMessages = [];

    /** @internal */
    protected array $dtoDefaults = [];

    /** @internal */
    protected array $dtoCasts = [];

    /** @internal */
    protected array $dtoMapData = [];

    /** @internal */
    protected array $dtoMapTransform = [];

    /** @internal */
    private static array $classReflections = [];

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public function __construct(?array $data = null)
    {
        if (is_null($data)) {
            return;
        }

        $this->buildAttributesData();
        $this->dtoData = $this->buildDataForValidation($data);

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

    public function __serialize(): array
    {
        return $this->jsonSerialize();
    }

    public function __unserialize(array $data): void
    {
        $this->__construct($data);
    }

    /**
     * Defines the default values for the properties of the DTO.
     */
    abstract protected function defaults(): array;

    /**
     * Defines the type casting for the properties of the DTO.
     */
    abstract protected function casts(): array;

    /**
     * Cast the given value to a DTO instance.
     *
     * @param  Model  $model
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
     * @param  Model  $model
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

    /*
     * JsonSerializable
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Maps the DTO properties before the DTO instantiation.
     */
    protected function mapData(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO transformation.
     */
    protected function mapToTransform(): array
    {
        return [];
    }

    /**
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function passedValidation(bool $forceCast = false): void
    {
        $this->validatedData = $this->validatedData($forceCast);
        /** @var array<Castable> $casts */
        $casts = $this->buildCasts();

        foreach ($this->validatedData as $key => $value) {
            $this->{$key} = $value;
        }

        $defaults = [
            ...$this->defaults(),
            ...$this->dtoDefaults,
        ];

        foreach ($defaults as $key => $value) {
            if (
                ! property_exists($this, $key) ||
                ! isset($this->{$key})
            ) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }

                    $this->{$key} = $value;
                    $this->validatedData[$key] = $value;

                    continue;
                }

                $formatted = $this->shouldReturnNull($key, $value)
                    ? null
                    : $this->castValue($casts[$key], $key, $value, $forceCast);

                $this->{$key} = $formatted;
                $this->validatedData[$key] = $formatted;
            }
        }

        $this->dtoData = [];
    }

    protected function failedValidation(): void
    {
        // Do nothing
    }

    protected function isValidData(): bool
    {
        return true;
    }

    /**
     * Builds the validated data from the given data and the rules.
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function validatedData(bool $forceCast = false): array
    {
        $acceptedKeys = $this->getAcceptedProperties();
        $result = [];

        /** @var array<Castable> $casts */
        $casts = $this->buildCasts();

        foreach ($this->dtoData as $key => $value) {
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
                    : $this->castValue($casts[$key], $key, $value, $forceCast);
            }
        }

        foreach ($acceptedKeys as $property) {
            if (! array_key_exists($property, $result)) {
                $this->{$property} = null;
            }
        }

        return $result;
    }

    /**
     * @throws CastTargetException
     */
    protected function castValue(mixed $cast, string $key, mixed $value, bool $forceCast = false): mixed
    {
        if ($this->lazyValidation && ! $forceCast) {
            return $value;
        }

        if ($cast instanceof Castable) {
            return $cast->cast($key, $value);
        }

        if (! is_callable($cast)) {
            throw new CastTargetException($key);
        }

        return $cast($key, $value);
    }

    protected function shouldReturnNull(string $key, mixed $value): bool
    {
        return is_null($value);
    }

    protected function buildCasts(): array
    {
        $casts = [];
        foreach ($this->dtoCasts as $property => $cast) {
            if (is_null($cast->param)) {
                $casts[$property] = new $cast->type();

                continue;
            }

            $param = match (true) {
                in_array($cast->type, [EnumCast::class, DTOCast::class]) => $cast->param,
                default => new $cast->param(),
            };

            $casts[$property] = new $cast->type($param);
        }

        return [
            ...$this->casts(),
            ...$casts,
        ];
    }

    protected function buildDataForExport(): array
    {
        $mapping = [
            ...$this->mapToTransform(),
            ...$this->dtoMapTransform,
        ];

        $data = $this->validatedData;
        foreach ($this->getAcceptedProperties() as $property) {
            if (! array_key_exists($property, $data) && isset($this->{$property})) {
                $data[$property] = $this->{$property};
            }
        }

        return $this->mapDTOData($mapping, $data);
    }

    protected function buildDataForValidation(array $data): array
    {
        $mapping = [
            ...$this->mapData(),
            ...$this->dtoMapData,
        ];

        return $this->mapDTOData($mapping, $data);
    }

    private function buildAttributesData(): void
    {
        $publicProperties = $this->getPublicProperties();

        $validatedProperties = $this->getPropertiesForAttribute($publicProperties, Rules::class);
        foreach ($validatedProperties as $property => $attribute) {
            $attributeInstance = $attribute->newInstance();
            $this->dtoRules[$property] = $attributeInstance->rules;
            $this->dtoMessages[$property] = $attributeInstance->messages ?? [];
        }

        $this->dtoMessages = array_filter(
            $this->dtoMessages,
            fn ($value) => $value !== []
        );

        $defaultProperties = $this->getPropertiesForAttribute($publicProperties, DefaultValue::class);
        foreach ($defaultProperties as $property => $attribute) {
            $attributeInstance = $attribute->newInstance();
            $this->dtoDefaults[$property] = $attributeInstance->value;
        }

        $castProperties = $this->getPropertiesForAttribute($publicProperties, Cast::class);
        foreach ($castProperties as $property => $attribute) {
            $attributeInstance = $attribute->newInstance();
            $this->dtoCasts[$property] = $attributeInstance;
        }

        $classReflection = $this->classReflection($this::class);
        $classAttributes = collect($classReflection->getAttributes());
        $lazyAttribute = $classAttributes->first(
            fn (ReflectionAttribute $attribute) => $attribute->getName() === Lazy::class
        );
        /** @var ReflectionAttribute $receiveAttribute */
        $receiveAttribute = $classAttributes->first(
            fn (ReflectionAttribute $attribute) => $attribute->getName() === Receive::class
        );
        /** @var ReflectionAttribute $provideAttribute */
        $provideAttribute = $classAttributes->first(
            fn (ReflectionAttribute $attribute) => $attribute->getName() === Provide::class
        );

        if (! is_null($lazyAttribute)) {
            $this->lazyValidation = true;
        }

        $receiveCase = null;
        $provideCase = null;
        if (! is_null($receiveAttribute)) {
            /** @var Receive $receive */
            $receive = $receiveAttribute->newInstance();
            $receiveCase = $receive->propertyCase;
        }

        if (! is_null($provideAttribute)) {
            /** @var Provide $provide */
            $provide = $provideAttribute->newInstance();
            $provideCase = $provide->propertyCase;
        }

        if (! is_null($receiveCase) || ! is_null($provideCase)) {
            foreach (array_keys($publicProperties) as $property) {
                if (! is_null($receiveCase)) {
                    $this->dtoMapData[$receiveCase->format($property)] = $property;
                }
                if (! is_null($provideCase)) {
                    $this->dtoMapTransform[$property] = $provideCase->format($property);
                }
            }
        }

        $mapDataProperties = $this->getPropertiesForAttribute($publicProperties, Map::class);
        foreach ($mapDataProperties as $property => $attribute) {
            $attributeInstance = $attribute->newInstance();

            if (! blank($attributeInstance->data)) {
                $this->dtoMapData[$attributeInstance->data] = $property;
            }

            if (! blank($attributeInstance->transform)) {
                $this->dtoMapTransform[$property] = $attributeInstance->transform;
            }
        }
    }

    private function getPublicProperties(): array
    {
        $reflectionClass = $this->classReflection($this::class);
        $dtoProperties = [];

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($this->isforbiddenProperty($property->getName())) {
                continue;
            }

            $reflectionProperty = new ReflectionProperty($this, $property->getName());
            $attributes = $reflectionProperty->getAttributes();
            $dtoProperties[$property->getName()] = $attributes;
        }

        return $dtoProperties;
    }

    private function getPropertiesForAttribute(array $properties, string $attribute): array
    {
        $result = [];
        foreach ($properties as $property => $attributes) {
            foreach ($attributes as $attr) {
                if ($attr->getName() === $attribute) {
                    $result[$property] = $attr;
                }
            }
        }

        return $result;
    }

    private function classReflection(string $class): ReflectionClass
    {
        if (! isset(self::$classReflections[$class])) {
            self::$classReflections[$class] = new ReflectionClass($class);
        }

        return self::$classReflections[$class];
    }

    private function mapDTOData(array $mapping, array $data): array
    {
        $mappedData = [];
        foreach ($data as $key => $value) {
            $properties = $this->getMappedProperties($mapping, $key);
            if ($properties !== [] && $this->isArrayable($value)) {
                $formatted = $this->formatArrayableValue($value);

                foreach ($properties as $property => $mappedValue) {
                    $mappedData[$mappedValue] = $formatted[$property] ?? null;
                }

                continue;
            }

            $property = array_key_exists($key, $mapping)
                ? $mapping[$key]
                : $key;

            if (isset($this->{$key}) && $value !== $this->{$key}) {
                $value = $this->{$key};
            }

            $mappedData[$property] = $this->isArrayable($value)
                ? $this->formatArrayableValue($value)
                : $value;
        }

        return $mappedData;
    }

    private function getMappedProperties(array $mapping, string $key): array
    {
        $properties = [];
        foreach ($mapping as $mappedKey => $mappedValue) {
            if (str_starts_with($mappedKey, "{$key}.")) {
                $arrayKey = str_replace("{$key}.", '', $mappedKey);
                $properties[$arrayKey] = $mappedValue;
            }

            if (str_starts_with($mappedValue, "{$key}.")) {
                $arrayKey = str_replace("{$key}.", '', $mappedValue);
                $properties[$arrayKey] = $mappedKey;
            }
        }

        return $properties;
    }

    private function isArrayable(mixed $value): bool
    {
        return is_array($value) ||
            $value instanceof Arrayable ||
            $value instanceof Collection ||
            $value instanceof ValidatedDTO ||
            $value instanceof Model ||
            (is_object($value) && ! ($value instanceof UploadedFile));
    }

    private function formatArrayableValue(mixed $value): array|int|string
    {
        return match (true) {
            is_array($value) => $value,
            $value instanceof BackedEnum => $value->value,
            $value instanceof UnitEnum => $value->name,
            $value instanceof Carbon || $value instanceof CarbonImmutable => $value->toISOString(true),
            $value instanceof Collection => $this->transformCollectionToArray($value),
            $value instanceof SimpleDTO => $this->transformDTOToArray($value),
            $value instanceof Arrayable => $value->toArray(),
            is_object($value) => (array) $value,
            default => [],
        };
    }

    private function transformCollectionToArray(Collection $collection): array
    {
        return $collection->map(fn ($item) => $this->isArrayable($item)
                ? $this->formatArrayableValue($item)
                : $item)->toArray();
    }

    private function transformDTOToArray(SimpleDTO $dto): array
    {
        $result = [];
        foreach ($dto->buildDataForExport() as $key => $value) {
            $result[$key] = $this->isArrayable($value)
                ? $this->formatArrayableValue($value)
                : $value;
        }

        return $result;
    }

    private function initConfig(): void
    {
        $config = config('dto');
        if (! empty($config) && array_key_exists('require_casting', $config)) {
            $this->requireCasting = $config['require_casting'];
        }
    }

    private function getAcceptedProperties(): array
    {
        $acceptedKeys = [];
        foreach (get_class_vars($this::class) as $key => $value) {
            if (! $this->isforbiddenProperty($key)) {
                $acceptedKeys[] = $key;
            }
        }

        return $acceptedKeys;
    }

    private function isforbiddenProperty(string $property): bool
    {
        return in_array($property, [
            'dtoData',
            'validatedData',
            'requireCasting',
            'validator',
            'dtoRules',
            'dtoMessages',
            'dtoDefaults',
            'dtoCasts',
            'dtoMapData',
            'dtoMapTransform',
            'lazyValidation',
        ]);
    }
}
