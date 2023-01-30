<?php

use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('properly casts to string')
    ->expect(fn () => new StringCast())
    ->cast(test_property(), 5)->toBe('5')
    ->cast(test_property(), 10.5)->toBe('10.5')
    ->cast(test_property(), true)->toBe('1')
    ->cast(test_property(), false)->toBe('');

it('throws exception when it is unable to cast property')
    ->expect(fn () => new StringCast())
    ->cast(test_property(), ['name' => 'John Doe'])
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');
