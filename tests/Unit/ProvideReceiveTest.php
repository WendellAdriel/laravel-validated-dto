<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Tests\Datasets\ProvideReceiveDTO;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

it('instantiates a ValidatedDTO receiving data in snake_case', function () {
    $firstName = fake()->firstName;
    $lastName = fake()->lastName;
    $age = fake()->numberBetween(18, 80);

    $validatedDTO = new ProvideReceiveDTO([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'age' => $age,
    ]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->firstName)->toBe($firstName)
        ->and($validatedDTO->lastName)->toBe($lastName)
        ->and($validatedDTO->age)->toBe($age);
});

it('provides data in PascalCase when calling toArray', function () {
    $firstName = fake()->firstName;
    $lastName = fake()->lastName;
    $age = fake()->numberBetween(18, 80);

    $validatedDTO = new ProvideReceiveDTO([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'age' => $age,
    ]);

    $array = $validatedDTO->toArray();

    expect($array)->toBe([
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Age' => $age,
    ]);
});

it('provides data in PascalCase when calling toJson', function () {
    $firstName = fake()->firstName;
    $lastName = fake()->lastName;
    $age = fake()->numberBetween(18, 80);

    $validatedDTO = new ProvideReceiveDTO([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'age' => $age,
    ]);

    $json = $validatedDTO->toJson();
    $decoded = json_decode($json, true);

    expect($decoded)->toBe([
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Age' => $age,
    ]);
});
