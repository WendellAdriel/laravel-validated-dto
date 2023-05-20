<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\ResourceDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\UserResourceDTO;

beforeEach(function () {
    $this->name = faker()->name;
    $this->age = faker()->numberBetween(1, 100);
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
