<?php

declare(strict_types=1);

use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use WendellAdriel\ValidatedDTO\Support\TypeScriptCollector;

it('returns null when class does not extend ValidatedDTO', function () {
    $class = new class() {};

    $reflection = new ReflectionClass($class);
    $collector = new TypeScriptCollector(TypeScriptTransformerConfig::create());

    $type = $collector->getTransformedType($reflection);

    expect($type)->toBeNull();
});

it('uses the TypeScriptTransformer for an eligible class', function () {
    eval('
        namespace App\Data {
            use WendellAdriel\ValidatedDTO\ValidatedDTO;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
            class TransformerTestDTO extends ValidatedDTO {
                use EmptyRules, EmptyCasts, EmptyDefaults;

                public string $name;
            }
        }
    ');

    $reflection = new ReflectionClass(\App\Data\TransformerTestDTO::class);

    // Provide a config with no other conflicting transformers
    $config = TypeScriptTransformerConfig::create()
        ->transformers([\WendellAdriel\ValidatedDTO\Support\TypeScriptTransformer::class]);

    $collector = new TypeScriptCollector($config);

    $type = $collector->getTransformedType($reflection);

    expect($type)->not->toBeNull()
        ->and($type->getTypeScriptName())->toBe('App.Data.TransformerTestDTO');
});
