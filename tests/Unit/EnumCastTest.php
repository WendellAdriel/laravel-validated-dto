<?php

declare(strict_types=1);

use WendellAdriel\ValidatedDTO\Casting\EnumCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\DummyBackedEnum;
use WendellAdriel\ValidatedDTO\Tests\Datasets\DummyEnum;

it('properly casts to enum')
    ->expect(fn () => new EnumCast(DummyEnum::class))
    ->cast(test_property(), 'ONE')
    ->toBe(DummyEnum::ONE);

it('throws exception when given class is not an enum')
    ->expect(fn () => new EnumCast(stdClass::class))
    ->cast(test_property(), 'one')
    ->throws(CastTargetException::class, 'The property: test_property has an invalid cast configuration');

it('throws exception when given value is not a valid enum value')
    ->expect(fn () => new EnumCast(DummyEnum::class))
    ->cast(test_property(), 'invalid')
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');

it('properly casts to backed enum')
    ->expect(fn () => new EnumCast(DummyBackedEnum::class))
    ->cast(test_property(), 'foo')
    ->toBe(DummyBackedEnum::FOO);

it('throws exception when given value is not a valid backed enum value')
    ->expect(fn () => new EnumCast(DummyBackedEnum::class))
    ->cast(test_property(), 'invalid')
    ->throws(CastException::class, 'Unable to cast property: test_property - invalid value');
