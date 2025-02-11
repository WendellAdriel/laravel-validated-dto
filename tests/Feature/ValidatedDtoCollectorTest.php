<?php

declare(strict_types=1);

use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use WendellAdriel\ValidatedDTO\Support\Typescript\ValidatedDtoCollector;

it('returns null when class does not extend ValidatedDTO', function () {
    $class = new class() {};

    $reflection = new ReflectionClass($class);
    $collector = new ValidatedDtoCollector(TypeScriptTransformerConfig::create());

    $type = $collector->getTransformedType($reflection);

    expect($type)->toBeNull();
});

it('uses the ValidatedDtoTransformer for an eligible class', function () {
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
        ->transformers([\WendellAdriel\ValidatedDTO\Support\Typescript\ValidatedDtoTransformer::class]);

    $collector = new ValidatedDtoCollector($config);

    $type = $collector->getTransformedType($reflection);

    expect($type)->not->toBeNull()
        ->and($type->getTypeScriptName())->toBe('App.Data.TransformerTestDTO');
});
