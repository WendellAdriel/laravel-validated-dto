<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use WendellAdriel\ValidatedDTO\Casting\CarbonCast;
use WendellAdriel\ValidatedDTO\Casting\CarbonImmutableCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('casts to carbon', function () {
    $castable = new CarbonCast();

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(test_property(), '-1 days');
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');
});

it('casts to carbon with timezone', function () {
    $castable = new CarbonCast('Europe/Lisbon');

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(test_property(), '-1 days');
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');

    $castable = new CarbonCast('Europe/Lisbon', 'Y-m-d');

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(Carbon::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d H:i:s');
    $this->expectException(CastException::class);
    $castable->cast(test_property(), $date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');
});

it('casts to carbon immutable', function () {
    $castable = new CarbonImmutableCast();

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(test_property(), '-1 days');
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');

    $castable = new CarbonImmutableCast('Europe/Lisbon');

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d', strtotime('-1 days'));
    $result = $castable->cast(test_property(), '-1 days');
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');

    $castable = new CarbonImmutableCast('Europe/Lisbon', 'Y-m-d');

    $date = date('Y-m-d');
    $result = $castable->cast(test_property(), $date);
    expect($result)->toBeInstanceOf(CarbonImmutable::class);
    $result = $result->format('Y-m-d');
    expect($result)->toBe($date);

    $date = date('Y-m-d H:i:s');
    $this->expectException(CastException::class);
    $castable->cast(test_property(), $date);

    $this->expectException(CastException::class);
    $castable->cast(test_property(), 'TEST');
});
