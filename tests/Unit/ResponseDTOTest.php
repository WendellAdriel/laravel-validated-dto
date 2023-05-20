<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\ResponseDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\UserResponseDTO;

beforeEach(function () {
    $this->name = faker()->name;
    $this->age = faker()->numberBetween(1, 100);
});

it('validates that a ResponseDTO can be converted to a JsonResponse', function () {
    $responseDTO = new UserResponseDTO(['name' => $this->name, 'age' => $this->age]);
    $response = $responseDTO->toResponse(new Request());

    expect($responseDTO)->toBeInstanceOf(ResponseDTO::class)
        ->and($responseDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBe($responseDTO->toArray())
        ->and($response->getStatusCode())
        ->toBe(200);
});

it('validates that a ResponseDTO can be converted to a JsonResponse with custom code', function () {
    $responseDTO = new UserResponseDTO(['name' => $this->name, 'age' => $this->age], 201);
    $response = $responseDTO->toResponse(new Request());

    expect($responseDTO)->toBeInstanceOf(ResponseDTO::class)
        ->and($responseDTO->validatedData)
        ->toBe(['name' => $this->name, 'age' => $this->age])
        ->and($response)
        ->toBeInstanceOf(JsonResponse::class)
        ->and((array) $response->getData())
        ->toBe($responseDTO->toArray())
        ->and($response->getStatusCode())
        ->toBe(201);
});
