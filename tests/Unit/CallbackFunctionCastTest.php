<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Exceptions\CastException;

it('casts property to object using callback function')
    ->expect(function () {
        $callback = function (string $property, mixed $value) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            if (! is_array($value)) {
                throw new CastException($property);
            }

            return (object) $value;
        };

        return $callback(test_property(), '{"name": "John Doe", "email": "john.doe@example.com"}');
    })
    ->toBeObject()
    ->toEqual((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com']);

it('casts property to uppercase using callback function')
    ->expect(function () {
        $callback = fn (string $property, mixed $value) => strtoupper($value);

        return $callback(test_property(), 'John Doe');
    })
    ->toEqual('JOHN DOE');
