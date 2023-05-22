<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Casting\BooleanCast;

it('properly casts from integer to true boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 1)
    ->toBeTrue();

it('properly casts from "true" to true boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 'true')
    ->toBeTrue();

it('properly casts from "yes" to true boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 'yes')
    ->toBeTrue();

it('properly casts integer to false boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 0)
    ->toBeFalse();

it('properly casts from "false" to false boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 'false')
    ->toBeFalse();

it('properly casts from "no" to false boolean')
    ->expect(fn () => new BooleanCast())
    ->cast(test_property(), 'no')
    ->toBeFalse();
