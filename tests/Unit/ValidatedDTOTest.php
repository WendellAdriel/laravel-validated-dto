<?php

namespace WendellAdriel\ValidatedDTO\Tests\Unit;

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\Tests\Dataset\ValidatedDTOInstance;
use WendellAdriel\ValidatedDTO\Tests\TestCase;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedDTOTest extends TestCase
{
    use WithFaker;

    private string $subject_name;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject_name = $this->faker->name();
    }

    public function testAValidatedDTOIsConstructedValidatingItsData(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $this->assertInstanceOf(ValidatedDTO::class, $validatedDTO);
        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testConstructingAValidatedDTOWithInvalidDataThrowsAnException(): void
    {
        $this->expectException(ValidationException::class);

        new ValidatedDTOInstance([]);
    }

    public function testItIsPossibleToSetAPropertyInAValidatedDTO(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $validatedDTO->age = 30;

        $this->assertEquals(30, $validatedDTO->age);
    }

    public function testNullWillBeReturnedWhenTryingToGetAPropertyThatDoesNotExist(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $this->assertNull($validatedDTO->age);
    }

    public function testAValidatedDTOCanBeConstructedFromJson(): void
    {
        $validatedDTO = ValidatedDTOInstance::fromJson(
            '{"name": "'.$this->subject_name.'"}'
        );

        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testConstructingAValidatedDTOFromInvalidJsonThrowsAnException(): void
    {
        $this->expectException(InvalidJsonException::class);

        ValidatedDTOInstance::fromJson(
            '{"name": "'.$this->subject_name.'"'
        );
    }

    public function testAValidatedDTOCanBeConstructedFromARequest(): void
    {
        $request = new Request(['name' => $this->subject_name]);

        $validatedDTO = ValidatedDTOInstance::fromRequest($request);

        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testAValidatedDTOCanBeConstructedFromAnEloquentModel(): void
    {
        $model = new class() extends Model
        {
            protected $fillable = ['name'];
        };

        $model->fill(['name' => $this->subject_name]);

        $validatedDTO = ValidatedDTOInstance::fromModel($model);

        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testAValidatedDTOCanBeConstructedFromCommandArguments(): void
    {
        $command = new class() extends Command
        {
            public function __invoke()
            {
            }

            protected $signature
                = 'test:command
                {name : The name of the user}';
        };

        Application::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        $this->artisan('test:command', ['name' => $this->subject_name]);

        $validatedDTO = ValidatedDTOInstance::fromCommandArguments($command);

        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testAValidatedDTOCanBeConstructedFromCommandOptions(): void
    {
        $command = new class() extends Command
        {
            public function __invoke()
            {
            }

            protected $signature
                = 'test:command
                {--name= : The name of the user}';
        };

        Application::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        $this->artisan('test:command', ['--name' => $this->subject_name]);

        $validatedDTO = ValidatedDTOInstance::fromCommandOptions($command);

        $this->assertEquals(
            ['name' => $this->subject_name],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testAValidatedDTOCanBeConstructedFromACommand(): void
    {
        $command = new class() extends Command
        {
            public function __invoke()
            {
            }

            protected $signature
                = 'test:command
                {name : The name of the user}
                {--age= : The age of the user}';
        };

        Application::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        $this->artisan(
            'test:command',
            ['name' => $this->subject_name, '--age' => 30]
        );

        $validatedDTO = ValidatedDTOInstance::fromCommand($command);

        $this->assertEquals(
            ['name' => $this->subject_name, 'age' => 30],
            $validatedDTO->validatedData
        );
        $this->assertTrue($validatedDTO->validator->passes());
    }

    public function testTheMethodToArrayReturnsTheValidatedData(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $this->assertEquals(['name' => $this->subject_name], $validatedDTO->toArray());
    }

    public function testTheMethodToJsonReturnsTheValidatedDataAsJson(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $this->assertEquals(
            '{"name":"'.$this->subject_name.'"}',
            $validatedDTO->toJson()
        );
    }

    public function testAModelInstanceCanBeCreatedFromAValidatedDTO(): void
    {
        $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

        $model = new class() extends Model
        {
            protected $fillable = ['name'];
        };

        $model_instance = $validatedDTO->toModel($model::class);

        $this->assertInstanceOf(Model::class, $model_instance);
        $this->assertEquals(
            ['name' => $this->subject_name],
            $model_instance->toArray()
        );
    }
}
