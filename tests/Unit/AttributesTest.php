<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\UserAttributesDTO;

beforeEach(function () {
    $this->subject_name = fake()->name;
    $this->subject_email = fake()->unique()->safeEmail;
});

it('throws exception when trying to instantiate a ValidatedDTO with invalid data using the Rules attribute')
    ->expect(fn () => new UserAttributesDTO([]))
    ->throws(ValidationException::class);

it('instantiates a ValidatedDTO validating its data using the Rules attribute and getting default values from the DefaultValue attribute', function () {
    $userDTO = new UserAttributesDTO([
        'name' => $this->subject_name,
        'email' => $this->subject_email,
    ]);

    expect($userDTO)->toBeInstanceOf(UserAttributesDTO::class)
        ->and($userDTO->validatedData)
        ->toBe([
            'name' => $this->subject_name,
            'email' => $this->subject_email,
            'active' => true,
        ])
        ->and($userDTO->validator->passes())
        ->toBeTrue();
});

it('maps the DTO data using the Map attribute', function () {
    $userDTO = new UserAttributesDTO([
        'user_name' => $this->subject_name,
        'email' => $this->subject_email,
    ]);

    expect($userDTO)->toBeInstanceOf(UserAttributesDTO::class)
        ->and($userDTO->validatedData)
        ->toBe([
            'name' => $this->subject_name,
            'email' => $this->subject_email,
            'active' => true,
        ])
        ->and($userDTO->validator->passes())
        ->toBeTrue()
        ->and($userDTO->toArray())
        ->toBe([
            'full_name' => $this->subject_name,
            'email' => $this->subject_email,
            'active' => true,
        ]);
});

it('casts the DTO data using the Cast attribute', function () {
    $userDTO = new UserAttributesDTO([
        'name' => $this->subject_name,
        'email' => $this->subject_email,
        'active' => '0',
        'age' => '25',
        'grades' => ['10', '9.5', '8.5'],
    ]);

    expect($userDTO)->toBeInstanceOf(UserAttributesDTO::class)
        ->and($userDTO->validatedData)
        ->toBe([
            'name' => $this->subject_name,
            'email' => $this->subject_email,
            'active' => false,
            'age' => 25,
            'grades' => [10.0, 9.5, 8.5],
        ])
        ->and($userDTO->validator->passes())
        ->toBeTrue()
        ->and($userDTO->toArray())
        ->toBe([
            'full_name' => $this->subject_name,
            'email' => $this->subject_email,
            'active' => false,
            'age' => 25,
            'grades' => [10.0, 9.5, 8.5],
        ]);
});
