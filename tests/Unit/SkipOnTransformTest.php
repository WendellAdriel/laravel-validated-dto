<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Tests\Datasets\SkipDTO;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

beforeEach(function () {
    $this->name = fake()->name;
    $this->age = fake()->numberBetween(1, 20);
});

it('skip attribute when transforming to array', function () {
    $dto = new SkipDTO([
        'name' => $this->name,
        'age' => $this->age,
    ]);

    expect($dto)->toBeInstanceOf(ValidatedDTO::class)
        ->and($dto->name)->toBe($this->name)
        ->and($dto->toArray())->toBe(['name' => $this->name]);
});

it('skip attribute when transforming to json', function () {
    $dto = new SkipDTO([
        'name' => $this->name,
        'age' => $this->age,
    ]);

    expect($dto)->toBeInstanceOf(ValidatedDTO::class)
        ->and($dto->name)->toBe($this->name)
        ->and($dto->toJson())->toBe('{"name":"' . $this->name . '"}');
});
