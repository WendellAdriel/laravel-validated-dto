<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\Relation;
use WendellAdriel\ValidatedDTO\Casting\MorphCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

beforeEach(function () {
    Relation::morphMap([
        'model_instance' => \WendellAdriel\ValidatedDTO\Tests\Datasets\ModelInstance::class,
    ]);
});

it('properly casts to the morphed model class', function () {
    $dto = new class()
    {
        public array $dtoData = [
            'test_property_type' => 'model_instance',
        ];

        public function castProperty($value)
        {
            $cast = new MorphCast();

            return $cast->cast('test_property', $value);
        }
    };

    $model = $dto->castProperty(['name' => 'Jane Doe', 'age' => 25]);

    expect($model)->toBeInstanceOf(\WendellAdriel\ValidatedDTO\Tests\Datasets\ModelInstance::class)
        ->and($model->toArray())->toBe(['name' => 'Jane Doe', 'age' => 25]);
});

it('throws exception if morph type key is missing', function () {
    $dto = new class()
    {
        public array $dtoData = [];

        public function castProperty($value)
        {
            $cast = new MorphCast();

            return $cast->cast('test_property', $value);
        }
    };

    $dto->castProperty(['name' => 'Jane Doe', 'age' => 25]);
})->throws(CastException::class, 'MorphCast: Missing morph type key [test_property_type] in DTO data.');

it('throws exception if model class is invalid', function () {
    $dto = new class()
    {
        public array $dtoData = [
            'test_property_type' => 'NonExistentModel',
        ];

        public function castProperty($value)
        {
            $cast = new MorphCast();

            return $cast->cast('test_property', $value);
        }
    };

    $dto->castProperty(['name' => 'Jane Doe', 'age' => 25]);
})->throws(CastException::class, 'MorphCast: Invalid model class [NonExistentModel].');
