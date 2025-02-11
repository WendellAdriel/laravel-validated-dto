<?php

declare(strict_types=1);

use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use WendellAdriel\ValidatedDTO\Support\TypeScriptTransformer;

it('returns null when class does not extend ValidatedDTO', function () {
    $class = new class()
    {
        public string $name;
    };

    $reflection = new ReflectionClass($class);

    $transformer = new TypeScriptTransformer(TypeScriptTransformerConfig::create());
    $type = $transformer->transform($reflection, 'IrrelevantName');

    expect($type)->toBeNull();
});

it('transforms a ValidatedDTO with public properties into a TransformedType', function () {
    eval('
        namespace App\Data {
            use WendellAdriel\ValidatedDTO\ValidatedDTO;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
            class TestTransformerDTO extends ValidatedDTO {
                use EmptyRules, EmptyCasts, EmptyDefaults;

                public string $name;
                public int $age;
                public static string $shouldNotAppear = "excluded";
                protected string $invisible = "excluded";
            }
        }
    ');

    $reflection = new ReflectionClass(\App\Data\TestTransformerDTO::class);

    $transformer = new TypeScriptTransformer(TypeScriptTransformerConfig::create());
    $type = $transformer->transform($reflection, 'TransformedDTO');

    // Should only include public, non-static properties
    expect($type)->toBeInstanceOf(TransformedType::class)
        ->and($type->name)->toBe('TransformedDTO')
        ->and($type->transformed)->toContain('name: string;')
        ->and($type->transformed)->toContain('age: number;')
        ->and($type->transformed)->not->toContain('shouldNotAppear')
        ->and($type->transformed)->not->toContain('invisible');
});

it('excludes properties listed in excludedProperties', function () {
    eval('
        namespace App\Data {
            use WendellAdriel\ValidatedDTO\ValidatedDTO;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
            class ExcludedPropertyDTO extends ValidatedDTO {
                use EmptyRules, EmptyCasts, EmptyDefaults;

                public bool $lazyValidation = true; // excluded by default
                public string $title;
            }
        }
    ');

    $reflection = new ReflectionClass(\App\Data\ExcludedPropertyDTO::class);

    $transformer = new TypeScriptTransformer(TypeScriptTransformerConfig::create());
    $type = $transformer->transform($reflection, 'ExcludedProps');

    expect($type->transformed)->not->toContain('lazyValidation:')
        ->and($type->transformed)->toContain('title: string;')
        ->and($type->getTypeScriptName())->toBe('App.Data.ExcludedProps');
});

it('transforms a ValidatedDTO with nested DTO and enum property', function () {
    eval('
        namespace App\Enums {
            enum FakeStatusEnum: string {
                case FIRST = "first";
                case SECOND = "second";
            }
        }
    ');

    eval('
        namespace App\Data {
            use WendellAdriel\ValidatedDTO\ValidatedDTO;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
            class ChildDTO extends ValidatedDTO {
                use EmptyRules, EmptyCasts, EmptyDefaults;

                public string $childField;
            }
        }
    ');

    eval('
        namespace App\Data {
            use WendellAdriel\ValidatedDTO\ValidatedDTO;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyRules;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
            use WendellAdriel\ValidatedDTO\Concerns\EmptyDefaults;
            use App\Enums\FakeStatusEnum;

            class ParentDTO extends ValidatedDTO {
                use EmptyRules, EmptyCasts, EmptyDefaults;

                public FakeStatusEnum $status;
                public ChildDTO $child;
            }
        }
    ');

    $reflection = new ReflectionClass(\App\Data\ParentDTO::class);
    $transformer = new TypeScriptTransformer(TypeScriptTransformerConfig::create());
    $type = $transformer->transform($reflection, 'ComplexDTO');

    expect($type)->toBeInstanceOf(TransformedType::class)
        ->and($type->name)->toBe('ComplexDTO')
        ->and($type->transformed)->toContain('status: {%App\Enums\FakeStatusEnum%};')
        ->and($type->transformed)->toContain('child: {%App\Data\ChildDTO%};')
        ->and($type->missingSymbols->all())
        // Missing Symbols contain references to other types. Once all types are
        // transformed, the package will replace these references with their
        // TypeScript types. When no type is found the type will default to any.
        ->toContain(\App\Enums\FakeStatusEnum::class)
        ->and($type->missingSymbols->all())->toContain(\App\Data\ChildDTO::class);
});
