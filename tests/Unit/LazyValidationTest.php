<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\LazyAttributeDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\LazyDTO;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

beforeEach(function () {
    $this->subject_name = fake()->name;
});

it('instantiates a ValidatedDTO marked as lazy without validating its data', function () {
    $validatedDTO = new LazyDTO(['name' => $this->subject_name]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();
});

it('does not fails a lazy validation with valid data', function () {
    $validatedDTO = new LazyDTO(['name' => $this->subject_name]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();

    $validatedDTO->validate();
});

it('fails a lazy validation with invalid data', function () {
    $validatedDTO = new LazyDTO(['name' => null]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => null])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();

    $validatedDTO->validate();
})->throws(ValidationException::class);

it('instantiates a ValidatedDTO with Lazy attribute without validating its data', function () {
    $name = fake()->name;
    $validatedDTO = new LazyAttributeDTO(['name' => $name]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => $name])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();
});

it('does not fail lazy validation with Lazy attribute when valid data is provided', function () {
    $name = fake()->name;
    $age = fake()->numberBetween(18, 80);
    $validatedDTO = new LazyAttributeDTO([
        'name' => $name,
        'age' => $age,
    ]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => $name, 'age' => $age])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();

    $validatedDTO->validate();
});

it('fails lazy validation with Lazy attribute when invalid data is provided', function () {
    $validatedDTO = new LazyAttributeDTO(['name' => null]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => null])
        ->and($validatedDTO->lazyValidation)
        ->toBeTrue();

    $validatedDTO->validate();
})->throws(ValidationException::class);
