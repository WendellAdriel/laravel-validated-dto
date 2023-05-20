<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ValidatedDTOInstance;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

it('casts to DTO', function () {
    $castable = new DTOCast(ValidatedDTOInstance::class);

    expect($castable)->cast(test_property(), '{"name": "John Doe", "age": 30}')
        ->toBeInstanceOf(ValidatedDTO::class);

    expect($castable)->cast(test_property(), '{"name": "John Doe", "age": 30}')
        ->toArray()
        ->toBe(['name' => 'John Doe', 'age' => 30]);

    expect($castable)->cast(test_property(), ['name' => 'John Doe', 'age' => 30])
        ->toBeInstanceOf(ValidatedDTO::class);

    expect($castable)->cast(test_property(), ['name' => 'John Doe', 'age' => 30])
        ->toArray()
        ->toEqual(['name' => 'John Doe', 'age' => 30]);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');

    $castable = new DTOCast(Model::class);

    $this->expectException(CastTargetException::class);
    $castable->cast(test_property(), ['name' => 'John Doe', 'age' => 30]);
});
