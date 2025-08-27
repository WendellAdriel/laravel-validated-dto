<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Casting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

class MorphCast
{
    /** @var array<string, mixed> */
    private array $resolvedDto;

    public function __construct()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

        throw_unless(is_array($trace[1]['object']->dtoData ?? null), new CastException(
            'MorphCast: Calling DTO instance does not have accessible dtoData array.',
        ));

        $this->resolvedDto = $trace[1]['object']->dtoData;
    }

    public function cast(string $property, mixed $value): Model
    {
        [$morphTypeKey, $morphIdKey] = $this->resolveMorphKeys($property);

        throw_unless(isset($this->resolvedDto[$morphTypeKey]), new CastException("MorphCast: Missing morph type key [{$morphTypeKey}] in DTO data."));

        $morphClassAlias = $this->resolvedDto[$morphTypeKey];

        $modelClass = Relation::getMorphedModel($morphClassAlias) ?? $morphClassAlias;

        throw_if(! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class), new CastException("MorphCast: Invalid model class [{$modelClass}]."));

        /** @var Model $modelInstance */
        $modelInstance = new $modelClass();

        // forceFill is correct here - we're in a DTO casting context with trusted data
        // and need to preserve all attributes (fillable + non-fillable like id, timestamps)
        if (is_array($value) && ! empty($value)) {
            $modelInstance->forceFill($value);
        }

        return $modelInstance;
    }

    /**
     * Resolve morph keys following Laravel convention.
     * Override this method to customize key resolution.
     */
    protected function resolveMorphKeys(string $property): array
    {
        return [
            "{$property}_type", // morph type key
            "{$property}_id",   // morph id key
        ];
    }
}
