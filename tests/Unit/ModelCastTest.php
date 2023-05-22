<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use WendellAdriel\ValidatedDTO\Casting\ModelCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ModelInstance;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ValidatedDTOInstance;

it('properly casts a to the Model class')
    ->expect(fn () => new ModelCast(ModelInstance::class))
    ->cast(test_property(), '{"name": "John Doe", "age": 30}')
    ->toBeInstanceOf(Model::class);

it('properly casts a json string to model')
    ->expect(fn () => new ModelCast(ModelInstance::class))
    ->cast(test_property(), '{"name": "John Doe", "age": 30}')
    ->toArray()
    ->toBe(['name' => 'John Doe', 'age' => 30]);

it('properly casts an array to the Model class')
    ->expect(fn () => new ModelCast(ModelInstance::class))
    ->cast(test_property(), ['name' => 'John Doe', 'age' => 30])
    ->toBeInstanceOf(Model::class);

it('properly casts an array string to model')
    ->expect(fn () => new ModelCast(ModelInstance::class))
    ->cast(test_property(), ['name' => 'John Doe', 'age' => 30])
    ->toArray()
    ->toBe(['name' => 'John Doe', 'age' => 30]);

it('throws exception when  the property has an invalid cast configuration')
    ->expect(fn () => new ModelCast(ValidatedDTOInstance::class))
    ->cast(test_property(), ['name' => 'John Doe', 'age' => 30])
    ->throws(CastTargetException::class, 'The property: test_property has an invalid cast configuration');
