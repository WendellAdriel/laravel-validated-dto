<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use WendellAdriel\ValidatedDTO\ResourceDTO;
use WendellAdriel\ValidatedDTO\Support\ResourceCollection;
use WendellAdriel\ValidatedDTO\Tests\Datasets\UserResourceDTO;

beforeEach(function () {
    $this->name = fake()->name;
    $this->age = fake()->numberBetween(1, 100);
});

it('validates that a ResourceDTO can be converted to a JsonResponse', function () {
    $resourceDTO = new UserResourceDTO(['name' => $this->name, 'age' => $this->age]);
    $response = $resourceDTO->toResponse(new Request());

    expect($resourceDTO)->toBeInstanceOf(ResourceDTO::class)
        ->and($resourceDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBe($resourceDTO->toArray())
        ->and($response->getStatusCode())
        ->toBe(200);
});

it('validates that a ResourceDTO can be converted to a JsonResponse with custom code', function () {
    $resourceDTO = new UserResourceDTO(['name' => $this->name, 'age' => $this->age], 201);
    $response = $resourceDTO->toResponse(new Request());

    expect($resourceDTO)->toBeInstanceOf(ResourceDTO::class)
        ->and($resourceDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBe($resourceDTO->toArray())
        ->and($response->getStatusCode())
        ->toBe(201);
});

it('validates that a ResourceDTO can return a collection of data into a JsonResponse', function () {
    $list = [
        ['name' => $this->name, 'age' => $this->age],
        ['name' => $this->name, 'age' => $this->age],
        ['name' => $this->name, 'age' => $this->age],
    ];

    $resourceDTO = UserResourceDTO::collection($list);
    $response = $resourceDTO->toResponse(new Request());

    expect($resourceDTO)->toBeInstanceOf(ResourceCollection::class)
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBeArray()
        ->toHaveCount(3)
        ->each()
        ->toHaveProperties(['name', 'age'])
        ->and($response->getStatusCode())
        ->toBe(200);
});

it('validates that a ResourceDTO can return a collection of data into a JsonResponse with custom code', function () {
    $list = [
        ['name' => $this->name, 'age' => $this->age],
        ['name' => $this->name, 'age' => $this->age],
        ['name' => $this->name, 'age' => $this->age],
    ];

    $resourceDTO = UserResourceDTO::collection($list, 201);
    $response = $resourceDTO->toResponse(new Request());

    expect($resourceDTO)->toBeInstanceOf(ResourceCollection::class)
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBeArray()
        ->toHaveCount(3)
        ->each()
        ->toHaveProperties(['name', 'age'])
        ->and($response->getStatusCode())
        ->toBe(201);
});
