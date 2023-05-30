<?php

declare(strict_types=1);

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\SimpleDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\CallableCastingDTOInstance;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleDTOInstance;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleMapBeforeExportDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleMapBeforeValidationDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleMapDataDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleMappedNameDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleNameDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleNullableDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\SimpleUserDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\User;

beforeEach(function () {
    $this->subject_name = faker()->name;
});

it('instantiates a SimpleDTO', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO)->toBeInstanceOf(SimpleDTO::class)
        ->and($simpleDTO->validatedData)
        ->toBe(['name' => $this->subject_name]);
});

it('instantiates a SimpleDTO with nullable and optional properties', function () {
    $dto = new SimpleNullableDTO([
        'name' => $this->subject_name,
        'address' => null,
    ]);

    expect($dto)->toBeInstanceOf(SimpleNullableDTO::class)
        ->and($dto->name)
        ->toBeString()
        ->and($dto->age)
        ->toBeNull()
        ->and($dto->address)
        ->toBeNull();
});

it('returns null when trying to access a property that does not exist', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO->age)->toBeNull();
});

it('validates that is possible to set a property in a SimpleDTO', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    $simpleDTO->age = 30;

    expect($simpleDTO->age)->toBe(30);
});

it('validates that a SimpleDTO can be instantiated from a JSON string', function () {
    $simpleDTO = SimpleDTOInstance::fromJson('{"name": "' . $this->subject_name . '"}');

    expect($simpleDTO->validatedData)
        ->toBe(['name' => $this->subject_name]);
});

it('throws exception when trying to instantiate a SimpleDTO from an invalid JSON string')
    ->expect(fn () => SimpleDTOInstance::fromJson('{"name": "' . $this->subject_name . '"'))
    ->throws(InvalidJsonException::class);

it('validates that a SimpleDTO can be instantiated from Array', function () {
    $simpleDTO = SimpleDTOInstance::fromArray(['name' => $this->subject_name]);

    expect($simpleDTO->validatedData)
        ->toBe(['name' => $this->subject_name]);
});

it('validates that a SimpleDTO can be instantiated from a Request', function () {
    $request = new Request(['name' => $this->subject_name]);

    $simpleDTO = SimpleDTOInstance::fromRequest($request);

    expect($simpleDTO->validatedData)
        ->toBe(['name' => $this->subject_name]);
});

it('validates that a SimpleDTO can be instantiated from an Eloquent Model', function () {
    $model = new class() extends Model
    {
        protected $fillable = ['name'];
    };

    $model->fill(['name' => $this->subject_name]);

    $simpleDTO = SimpleDTOInstance::fromModel($model);

    expect($simpleDTO->validatedData)
        ->toBe(['name' => $this->subject_name]);
});

it('validates that a SimpleDTO can be instantiated from Command arguments', function () {
    $command = new class() extends Command
    {
        protected $signature
            = 'test:command
            {name : The name of the user}';

        public function __invoke()
        {
        }
    };

    Application::starting(function ($artisan) use ($command) {
        $artisan->add($command);
    });

    $this->artisan('test:command', ['name' => $this->subject_name]);

    $simpleDTO = SimpleDTOInstance::fromCommandArguments($command);

    expect($simpleDTO->validatedData)
        ->toHaveKey('name', $this->subject_name);
});

it('validates that a SimpleDTO can be instantiated from Command options', function () {
    $command = new class() extends Command
    {
        protected $signature
            = 'test:command
            {--name= : The name of the user}';

        public function __invoke()
        {
        }
    };

    Application::starting(function ($artisan) use ($command) {
        $artisan->add($command);
    });

    $this->artisan('test:command', ['--name' => $this->subject_name]);

    $simpleDTO = SimpleDTOInstance::fromCommandOptions($command);

    expect($simpleDTO->validatedData)
        ->toHaveKey('name', $this->subject_name);
});

it('validates that a SimpleDTO can be instantiated from a Command', function () {
    $command = new class() extends Command
    {
        protected $signature
            = 'test:command
            {name : The name of the user}
            {--age= : The age of the user}';

        public function __invoke()
        {
        }
    };

    Application::starting(function ($artisan) use ($command) {
        $artisan->add($command);
    });

    $this->artisan(
        'test:command',
        ['name' => $this->subject_name, '--age' => 30]
    );

    $simpleDTO = SimpleDTOInstance::fromCommand($command);

    expect($simpleDTO->validatedData)
        ->toHaveKey('name', $this->subject_name)
        ->toHaveKey('age', 30);
});

it('validates that the SimpleDTO can be converted into an array', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO)->toArray()
        ->toBe(['name' => $this->subject_name]);
});

it('validates that the SimpleDTO can be converted into a JSON string', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO)->toJson()
        ->toBe('{"name":"' . $this->subject_name . '"}');
});

it('validates that the SimpleDTO can be converted into a pretty JSON string with flag', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO)->toJson(true)
        ->toBe(json_encode(['name' => $this->subject_name], JSON_PRETTY_PRINT));
});

it('validates that the SimpleDTO can be converted into a pretty JSON string', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    expect($simpleDTO)->toPrettyJson()
        ->toBe(json_encode(['name' => $this->subject_name], JSON_PRETTY_PRINT));
});

it('validates that the SimpleDTO can be converted into an Eloquent Model', function () {
    $simpleDTO = new SimpleDTOInstance(['name' => $this->subject_name]);

    $model = new class() extends Model
    {
        protected $fillable = ['name'];
    };

    $model_instance = $simpleDTO->toModel($model::class);

    expect($model_instance)
        ->toBeInstanceOf(Model::class)
        ->toArray()
        ->toBe(['name' => $this->subject_name]);
});

it('maps data before validation', function () {
    $dto = SimpleMapBeforeValidationDTO::fromArray(['full_name' => $this->subject_name]);

    expect($dto->full_name)
        ->toBeNull()
        ->and($dto->name)
        ->toBe($this->subject_name);
});

it('maps data before export', function () {
    $dto = SimpleMapBeforeExportDTO::fromArray(['name' => $this->subject_name]);

    expect($dto->name)
        ->toBe($this->subject_name)
        ->and($dto->username)
        ->toBeNull()
        ->and($dto->toArray())
        ->toBe(['username' => $this->subject_name]);
});

it('maps data before validation and before export', function () {
    $dto = SimpleMapDataDTO::fromArray(['full_name' => $this->subject_name]);

    expect($dto->full_name)
        ->toBeNull()
        ->and($dto->name)
        ->toBe($this->subject_name)
        ->and($dto->username)
        ->toBeNull()
        ->and($dto->toArray())
        ->toBe(['username' => $this->subject_name]);
});

it('maps nested data to flat data before validation', function () {
    $dto = SimpleMappedNameDTO::fromArray([
        'name' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
    ]);

    expect($dto->first_name)
        ->toBe('John')
        ->and($dto->last_name)
        ->toBe('Doe');
});

it('maps nested data to flat data before export', function () {
    $dto = SimpleUserDTO::fromArray([
        'name' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
        'email' => 'john.doe@example.com',
    ]);

    $user = $dto->toModel(User::class);

    expect($dto->name)
        ->toBeInstanceOf(SimpleNameDTO::class)
        ->and($dto->name->first_name)
        ->toBe('John')
        ->and($dto->name->last_name)
        ->toBe('Doe')
        ->and($dto->email)
        ->toBe('john.doe@example.com')
        ->and($user->first_name)
        ->toBe('John')
        ->and($user->last_name)
        ->toBe('Doe')
        ->and($user->email)
        ->toBe('john.doe@example.com');
});

it('casts properties with castable classes and callables', function () {
    $dto = CallableCastingDTOInstance::fromArray([
        'name' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
        'age' => '30',
    ]);

    expect($dto->name)
        ->toBeInstanceOf(SimpleNameDTO::class)
        ->and($dto->name->first_name)
        ->toBe('John')
        ->and($dto->name->last_name)
        ->toBe('Doe')
        ->and($dto->age)
        ->toBe(30);
});
