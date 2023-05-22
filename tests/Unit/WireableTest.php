<?php

declare(strict_types=1);

use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\SimpleDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\WireableDTO;

beforeEach(function () {
    $this->name = faker()->name;
    $this->age = faker()->numberBetween(1, 100);
});

it('validates that a Wireable DTO will return the correct data for the toLivewire method', function () {
    $wireableDTO = new WireableDTO(['name' => $this->name, 'age' => $this->age]);

    expect($wireableDTO)->toBeInstanceOf(SimpleDTO::class)
        ->and($wireableDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($wireableDTO->toLivewire())
        ->toBe($wireableDTO->toArray());
});

it('validates that a Wireable DTO can be instantiated with the fromLivewire method with array', function () {
    $wireableDTO = WireableDTO::fromLivewire(['name' => $this->name, 'age' => $this->age]);

    expect($wireableDTO)
        ->toBeInstanceOf(SimpleDTO::class)
        ->and($wireableDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($wireableDTO->toLivewire())
        ->toBe($wireableDTO->toArray());
});

it('validates that a Wireable DTO can be instantiated with the fromLivewire method with collection', function () {
    $wireableDTO = WireableDTO::fromLivewire(collect(['name' => $this->name, 'age' => $this->age]));

    expect($wireableDTO)
        ->toBeInstanceOf(SimpleDTO::class)
        ->and($wireableDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($wireableDTO->toLivewire())
        ->toBe($wireableDTO->toArray());
});

it('validates that a Wireable DTO can be instantiated with the fromLivewire method with object', function () {
    $wireableDTO = WireableDTO::fromLivewire((object) ['name' => $this->name, 'age' => $this->age]);

    expect($wireableDTO)
        ->toBeInstanceOf(SimpleDTO::class)
        ->and($wireableDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($wireableDTO->toLivewire())
        ->toBe($wireableDTO->toArray());
});

it('validates that a Wireable DTO will be empty when instantiated with the fromLivewire method with invalid values', function ($value) {
    $wireableDTO = WireableDTO::fromLivewire($value);

    expect($wireableDTO)
        ->toBeInstanceOf(SimpleDTO::class)
        ->and($wireableDTO->validatedData)
        ->toBe([])
        ->and($wireableDTO->toLivewire())
        ->toBe([]);
})->with([
    null,
    true,
    false,
    'string',
    10.5,
    10,
    new stdClass(),
]);
