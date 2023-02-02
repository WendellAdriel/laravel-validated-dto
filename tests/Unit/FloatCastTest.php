<?php

use WendellAdriel\ValidatedDTO\Casting\FloatCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('properly casts to float')
    ->expect(fn () => new FloatCast())
    ->cast(test_property(), '10.5')
    ->toBe(10.5);

it('throws exception when it is unable to cast property')
    ->expect(fn () => new FloatCast())
    ->cast(test_property(), 'TEST')
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');
