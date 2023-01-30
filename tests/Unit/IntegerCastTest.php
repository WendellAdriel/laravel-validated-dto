<?php

use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('properly casts to integer')
    ->expect(fn () => new IntegerCast())
    ->cast(test_property(), '5')
    ->toBe(5);

it('throws exception when it is unable to cast property')
    ->expect(fn () => new IntegerCast())
    ->cast(test_property(), 'TEST')
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');
