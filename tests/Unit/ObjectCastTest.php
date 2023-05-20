<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Casting\ObjectCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('properly casts to object')
    ->expect(fn () => new ObjectCast())
    ->cast(test_property(), '{"name": "John Doe", "email": "john.doe@example.com"}')
    ->toBeObject()
    ->toEqual((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com']);

it('throws exception when it is unable to cast property')
    ->expect(fn () => new ObjectCast())
    ->cast(test_property(), 'TEST')
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');
