<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Support\Typescript;

use ReflectionClass;
use Spatie\TypeScriptTransformer\Collectors\DefaultCollector;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\TypeReflectors\ClassTypeReflector;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedDtoCollector extends DefaultCollector
{
    public function getTransformedType(ReflectionClass $class): ?TransformedType
    {
        if (! $this->shouldCollect($class)) {
            return null;
        }

        $reflector = ClassTypeReflector::create($class);

        // Always use our ValidatedDtoTransformer
        $transformer = $this->config->buildTransformer(ValidatedDtoTransformer::class);

        return $transformer->transform(
            $reflector->getReflectionClass(),
            $reflector->getName()
        );
    }

    protected function shouldCollect(ReflectionClass $class): bool
    {
        // Only collect classes that extend ValidatedDTO
        if (! $class->isSubclassOf(ValidatedDTO::class)) {
            return false;
        }

        return true;
    }
}
