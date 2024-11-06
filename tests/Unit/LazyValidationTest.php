<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
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
