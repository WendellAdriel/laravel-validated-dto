<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Support;

use ReflectionClass;
use ReflectionProperty;
use Spatie\TypeScriptTransformer\Structures\MissingSymbolsCollection;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;
use Spatie\TypeScriptTransformer\Transformers\TransformsTypes;
use Spatie\TypeScriptTransformer\TypeProcessors\ReplaceDefaultsTypeProcessor;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use WendellAdriel\ValidatedDTO\SimpleDTO;

class TypeScriptTransformer implements Transformer
{
    use TransformsTypes;

    protected TypeScriptTransformerConfig $config;

    /**
     * Properties to exclude from the TypeScript output
     */
    protected array $excludedProperties = [
        'lazyValidation',
    ];

    public function __construct(TypeScriptTransformerConfig $config)
    {
        $this->config = $config;
    }

    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        if (! $this->canTransform($class)) {
            return null;
        }

        $missingSymbols = new MissingSymbolsCollection();
        $properties = $this->transformProperties($class, $missingSymbols);

        return TransformedType::create(
            $class,
            $name,
            '{' . PHP_EOL . $properties . '}',
            $missingSymbols
        );
    }

    protected function canTransform(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(SimpleDTO::class);
    }

    protected function transformProperties(
        ReflectionClass $class,
        MissingSymbolsCollection $missingSymbols
    ): string {
        $properties = array_filter(
            $class->getProperties(ReflectionProperty::IS_PUBLIC),
            function (ReflectionProperty $property) {
                // Exclude static properties
                if ($property->isStatic()) {
                    return false;
                }

                // Exclude specific properties by name
                if (in_array($property->getName(), $this->excludedProperties)) {
                    return false;
                }

                return true;
            }
        );

        return array_reduce(
            $properties,
            function (string $carry, ReflectionProperty $property) use ($missingSymbols) {
                $transformed = $this->reflectionToTypeScript(
                    $property,
                    $missingSymbols,
                    false,
                    new ReplaceDefaultsTypeProcessor($this->config->getDefaultTypeReplacements())
                );

                if ($transformed === null) {
                    return $carry;
                }

                $propertyName = $property->getName();

                return "{$carry}{$propertyName}: {$transformed};" . PHP_EOL;
            },
            ''
        );
    }
}
