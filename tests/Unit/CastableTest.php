<?php

namespace WendellAdriel\ValidatedDTO\Tests\Unit;

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
use WendellAdriel\ValidatedDTO\Tests\Dataset\ModelInstance;
use WendellAdriel\ValidatedDTO\Tests\Dataset\ValidatedDTOInstance;
use WendellAdriel\ValidatedDTO\Tests\TestCase;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class CastableTest extends TestCase
{
    private string $testProperty = 'test_property';

    public function testCastToArray()
    {
        $castable = new ArrayCast();

        $result = $castable->cast($this->testProperty, '{"name": "John Doe", "email": "john.doe@example.com"}');
        $this->assertIsArray($result);
        $this->assertEquals(['name' => 'John Doe', 'email' => 'john.doe@example.com'], $result);

        $result = $castable->cast($this->testProperty, 'Test');
        $this->assertIsArray($result);
        $this->assertEquals(['Test'], $result);

        $result = $castable->cast($this->testProperty, 1);
        $this->assertIsArray($result);
        $this->assertEquals([1], $result);

        $result = $castable->cast($this->testProperty, ['A', 1]);
        $this->assertIsArray($result);
        $this->assertEquals(['A', 1], $result);
    }

    public function testCastToBoolean()
    {
        $castable = new BooleanCast();

        $result = $castable->cast($this->testProperty, 1);
        $this->assertIsBool($result);
        $this->assertTrue($result);

        $result = $castable->cast($this->testProperty, 'true');
        $this->assertIsBool($result);
        $this->assertTrue($result);

        $result = $castable->cast($this->testProperty, 'yes');
        $this->assertIsBool($result);
        $this->assertTrue($result);

        $result = $castable->cast($this->testProperty, 0);
        $this->assertIsBool($result);
        $this->assertNotTrue($result);

        $result = $castable->cast($this->testProperty, 'false');
        $this->assertIsBool($result);
        $this->assertNotTrue($result);

        $result = $castable->cast($this->testProperty, 'no');
        $this->assertIsBool($result);
        $this->assertNotTrue($result);
    }

    public function testCastToCarbon()
    {
        $castable = new CarbonCast();

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $date = date('Y-m-d', strtotime('-1 days'));
        $result = $castable->cast($this->testProperty, '-1 days');
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new CarbonCast('Europe/Lisbon');

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $date = date('Y-m-d', strtotime('-1 days'));
        $result = $castable->cast($this->testProperty, '-1 days');
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new CarbonCast('Europe/Lisbon', 'Y-m-d');

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');
    }

    public function testCastToCarbonImmutable()
    {
        $castable = new CarbonImmutableCast();

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(CarbonImmutable::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $date = date('Y-m-d', strtotime('-1 days'));
        $result = $castable->cast($this->testProperty, '-1 days');
        $this->assertInstanceOf(CarbonImmutable::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new CarbonImmutableCast('Europe/Lisbon');

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(CarbonImmutable::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $date = date('Y-m-d', strtotime('-1 days'));
        $result = $castable->cast($this->testProperty, '-1 days');
        $this->assertInstanceOf(CarbonImmutable::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new CarbonImmutableCast('Europe/Lisbon', 'Y-m-d');

        $date = date('Y-m-d');
        $result = $castable->cast($this->testProperty, $date);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertTrue($date === $result->format('Y-m-d'));

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');
    }

    public function testCastToCollection()
    {
        $castable = new CollectionCast();

        $result = $castable->cast($this->testProperty, '{"name": "John Doe", "email": "john.doe@example.com"}');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['name' => 'John Doe', 'email' => 'john.doe@example.com'], $result->toArray());

        $result = $castable->cast($this->testProperty, 'Test');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['Test'], $result->toArray());

        $result = $castable->cast($this->testProperty, 1);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals([1], $result->toArray());

        $result = $castable->cast($this->testProperty, ['A', 1]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['A', 1], $result->toArray());

        $castable = new CollectionCast(new BooleanCast());

        $result = $castable->cast($this->testProperty, [1, 'true', 'yes']);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals([true, true, true], $result->toArray());

        $castable = new CollectionCast(new IntegerCast());

        $result = $castable->cast($this->testProperty, ['1', '5', '10']);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals([1, 5, 10], $result->toArray());

        $castable = new CollectionCast(new DTOCast(ValidatedDTOInstance::class));

        $dataToCast = [
            ['name' => 'John Doe', 'age' => 30],
            ['name' => 'Mary Doe', 'age' => 25],
        ];

        $johnDto = new ValidatedDTOInstance(['name' => 'John Doe', 'age' => 30]);
        $maryDto = new ValidatedDTOInstance(['name' => 'Mary Doe', 'age' => 25]);

        $result = $castable->cast($this->testProperty, $dataToCast);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(
            [$johnDto->toArray(), $maryDto->toArray()],
            $result->map(fn (ValidatedDTO $dto) => $dto->toArray())->toArray()
        );
    }

    public function testCastToDTO()
    {
        $castable = new DTOCast(ValidatedDTOInstance::class);

        $result = $castable->cast($this->testProperty, '{"name": "John Doe", "age": 30}');
        $this->assertInstanceOf(ValidatedDTO::class, $result);
        $this->assertEquals(['name' => 'John Doe', 'age' => 30], $result->toArray());

        $result = $castable->cast($this->testProperty, ['name' => 'John Doe', 'age' => 30]);
        $this->assertInstanceOf(ValidatedDTO::class, $result);
        $this->assertEquals(['name' => 'John Doe', 'age' => 30], $result->toArray());

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new DTOCast(Model::class);

        $this->expectException(CastTargetException::class);
        $castable->cast($this->testProperty, ['name' => 'John Doe', 'age' => 30]);
    }

    public function testCastToFloat()
    {
        $castable = new FloatCast();

        $result = $castable->cast($this->testProperty, '10.5');
        $this->assertIsFloat($result);
        $this->assertEquals(10.5, $result);

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');
    }

    public function testCastToInteger()
    {
        $castable = new IntegerCast();

        $result = $castable->cast($this->testProperty, '5');
        $this->assertIsInt($result);
        $this->assertEquals(5, $result);

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');
    }

    public function testCastToModel()
    {
        $castable = new ModelCast(ModelInstance::class);

        $result = $castable->cast($this->testProperty, '{"name": "John Doe", "age": 30}');
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals(['name' => 'John Doe', 'age' => 30], $result->toArray());

        $result = $castable->cast($this->testProperty, ['name' => 'John Doe', 'age' => 30]);
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals(['name' => 'John Doe', 'age' => 30], $result->toArray());

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');

        $castable = new ModelCast(ValidatedDTOInstance::class);

        $this->expectException(CastTargetException::class);
        $castable->cast($this->testProperty, ['name' => 'John Doe', 'age' => 30]);
    }

    public function testCastToObject()
    {
        $castable = new ObjectCast();

        $result = $castable->cast($this->testProperty, '{"name": "John Doe", "email": "john.doe@example.com"}');
        $this->assertIsObject($result);
        $this->assertEquals((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com'], $result);

        $result = $castable->cast($this->testProperty, ['name' => 'John Doe', 'email' => 'john.doe@example.com']);
        $this->assertIsObject($result);
        $this->assertEquals((object) ['name' => 'John Doe', 'email' => 'john.doe@example.com'], $result);

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, 'TEST');
    }

    public function testCastToString()
    {
        $castable = new StringCast();

        $result = $castable->cast($this->testProperty, 5);
        $this->assertIsString($result);
        $this->assertEquals('5', $result);

        $result = $castable->cast($this->testProperty, 10.5);
        $this->assertIsString($result);
        $this->assertEquals('10.5', $result);

        $result = $castable->cast($this->testProperty, true);
        $this->assertIsString($result);
        $this->assertEquals('1', $result);

        $result = $castable->cast($this->testProperty, false);
        $this->assertIsString($result);
        $this->assertEquals('', $result);

        $this->expectException(CastException::class);
        $castable->cast($this->testProperty, ['name' => 'John Doe']);
    }
}
