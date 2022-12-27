<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use WendellAdriel\ValidatedDTO\Casting\ArrayCast;
use WendellAdriel\ValidatedDTO\Casting\BooleanCast;
use WendellAdriel\ValidatedDTO\Casting\CarbonCast;
use WendellAdriel\ValidatedDTO\Casting\CarbonImmutableCast;
use WendellAdriel\ValidatedDTO\Casting\CollectionCast;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Casting\FloatCast;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\ModelCast;
use WendellAdriel\ValidatedDTO\Casting\ObjectCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ModelInstance;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ValidatedDTOInstance;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

const TEST_PROPERTY = 'test_property';

it('casts to array', function () {
    $castable = new ArrayCast();

    $result = $castable->cast(TEST_PROPERTY, '{"name": "John Doe", "email": "john.doe@example.com"}');
    expect($result)
        ->toBeArray()
        ->toEqual(['name' => 'John Doe', 'email' => 'john.doe@example.com']);

    $result = $castable->cast(TEST_PROPERTY, 'Test');
    expect($result)
        ->toBeArray()
        ->toEqual(['Test']);

    $result = $castable->cast(TEST_PROPERTY, 1);
    expect($result)
        ->toBeArray()
        ->toEqual([1]);

    $result = $castable->cast(TEST_PROPERTY, ['A', 1]);
    expect($result)
        ->toBeArray()
        ->toEqual(['A', 1]);
});

it('casts to boolean', function () {
    $castable = new BooleanCast();

    $result = $castable->cast(TEST_PROPERTY, 1);
    expect($result)
        ->toBeBool()
        ->toBeTrue();

    $result = $castable->cast(TEST_PROPERTY, 'true');
    expect($result)
        ->toBeBool()
        ->toBeTrue();

    $result = $castable->cast(TEST_PROPERTY, 'yes');
    expect($result)
        ->toBeBool()
        ->toBeTrue();

    $result = $castable->cast(TEST_PROPERTY, 0);
    expect($result)
        ->toBeBool()
        ->toBeFalse();

    $result = $castable->cast(TEST_PROPERTY, 'false');
    expect($result)
        ->toBeBool()
        ->toBeFalse();

    $result = $castable->cast(TEST_PROPERTY, 'no');
    expect($result)
        ->toBeBool()
        ->toBeFalse();
});

it('casts to carbon', function () {
    $castable = new CarbonCast();

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(TEST_PROPERTY, '-1 days');
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new CarbonCast('Europe/Lisbon');

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(TEST_PROPERTY, '-1 days');
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new CarbonCast('Europe/Lisbon', 'Y-m-d');

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d H:i:s');
    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, $date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');
});

it('casts to carbon immutable', function () {
    $castable = new CarbonImmutableCast();

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(TEST_PROPERTY, '-1 days');
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new CarbonImmutableCast('Europe/Lisbon');

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(TEST_PROPERTY, '-1 days');
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new CarbonImmutableCast('Europe/Lisbon', 'Y-m-d');

    $date = date('Y-m-d');
    $result = $castable->cast(TEST_PROPERTY, $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d H:i:s');
    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, $date);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');
});

it('casts to collection', function () {
    $castable = new CollectionCast();

    $result = $castable->cast(TEST_PROPERTY, '{"name": "John Doe", "email": "john.doe@example.com"}');
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual(['name' => 'John Doe', 'email' => 'john.doe@example.com']);

    $result = $castable->cast(TEST_PROPERTY, 'Test');
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual(['Test']);

    $result = $castable->cast(TEST_PROPERTY, 1);
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual([1]);

    $result = $castable->cast(TEST_PROPERTY, ['A', 1]);
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual(['A', 1]);

    $castable = new CollectionCast(new BooleanCast());

    $result = $castable->cast(TEST_PROPERTY, [1, 'true', 'yes']);
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual([true, true, true]);

    $castable = new CollectionCast(new IntegerCast());

    $result = $castable->cast(TEST_PROPERTY, ['1', '5', '10']);
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->toArray();
    expect($result)->toEqual([1, 5, 10]);

    $castable = new CollectionCast(new DTOCast(ValidatedDTOInstance::class));

    $dataToCast = [
        ['name' => 'John Doe', 'age' => 30],
        ['name' => 'Mary Doe', 'age' => 25],
    ];

    $johnDto = new ValidatedDTOInstance(['name' => 'John Doe', 'age' => 30]);
    $maryDto = new ValidatedDTOInstance(['name' => 'Mary Doe', 'age' => 25]);

    $result = $castable->cast(TEST_PROPERTY, $dataToCast);
    expect($result)->toBeInstanceOf(Collection::class);
    $result = $result->map(fn (ValidatedDTO $dto) => $dto->toArray())->toArray();
    expect($result)->toEqual([$johnDto->toArray(), $maryDto->toArray()]);
});

it('casts to DTO', function () {
    $castable = new DTOCast(ValidatedDTOInstance::class);

    $result = $castable->cast(TEST_PROPERTY, '{"name": "John Doe", "age": 30}');
    expect($result)->toBeInstanceOf(ValidatedDTO::class);
    $result = $result->toArray();
    expect($result)->toEqual(['name' => 'John Doe', 'age' => 30]);

    $result = $castable->cast(TEST_PROPERTY, ['name' => 'John Doe', 'age' => 30]);
    expect($result)->toBeInstanceOf(ValidatedDTO::class);
    $result = $result->toArray();
    expect($result)->toEqual(['name' => 'John Doe', 'age' => 30]);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new DTOCast(Model::class);

    $this->expectException(CastTargetException::class);
    $castable->cast(TEST_PROPERTY, ['name' => 'John Doe', 'age' => 30]);
});

it('casts to float', function () {
    $castable = new FloatCast();

    $result = $castable->cast(TEST_PROPERTY, '10.5');
    expect($result)
        ->toBeFloat()
        ->toBe(10.5);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');
});

it('casts to integer', function () {
    $castable = new IntegerCast();

    $result = $castable->cast(TEST_PROPERTY, '5');
    expect($result)
        ->toBeInt()
        ->toBe(5);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');
});

it('casts to model', function () {
    $castable = new ModelCast(ModelInstance::class);

    $result = $castable->cast(TEST_PROPERTY, '{"name": "John Doe", "age": 30}');
    expect($result)->toBeInstanceOf(Model::class);
    $result = $result->toArray();
    expect($result)->toEqual(['name' => 'John Doe', 'age' => 30]);

    $result = $castable->cast(TEST_PROPERTY, ['name' => 'John Doe', 'age' => 30]);
    expect($result)->toBeInstanceOf(Model::class);
    $result = $result->toArray();
    expect($result)->toEqual(['name' => 'John Doe', 'age' => 30]);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');

    $castable = new ModelCast(ValidatedDTOInstance::class);

    $this->expectException(CastTargetException::class);
    $castable->cast(TEST_PROPERTY, ['name' => 'John Doe', 'age' => 30]);
});

it('casts to object', function () {
    $castable = new ObjectCast();

    $result = $castable->cast(TEST_PROPERTY, '{"name": "John Doe", "email": "john.doe@example.com"}');
    expect($result)
        ->toBeObject()
        ->toEqual((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com']);

    $result = $castable->cast(TEST_PROPERTY, ['name' => 'John Doe', 'email' => 'john.doe@example.com']);
    expect($result)
        ->toBeObject()
        ->toEqual((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com']);

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, 'TEST');
});

it('casts to string', function () {
    $castable = new StringCast();

    $result = $castable->cast(TEST_PROPERTY, 5);
    expect($result)
        ->toBeString()
        ->toBe('5');

    $result = $castable->cast(TEST_PROPERTY, 10.5);
    expect($result)
        ->toBeString()
        ->toBe('10.5');

    $result = $castable->cast(TEST_PROPERTY, true);
    expect($result)
        ->toBeString()
        ->toBe('1');

    $result = $castable->cast(TEST_PROPERTY, false);
    expect($result)
        ->toBeString()
        ->toBe('');

    $this->expectException(CastException::class);
    $castable->cast(TEST_PROPERTY, ['name' => 'John Doe']);
});
