<?php

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\Tests\Datasets\MapBeforeExportDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\MapBeforeValidationDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\MapDataDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\MappedNameDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\NameDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\NullableDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\User;
use WendellAdriel\ValidatedDTO\Tests\Datasets\UserDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ValidatedDTOInstance;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

beforeEach(function () {
    $this->subject_name = faker()->name;
});

it('instantiates a ValidatedDTO validating its data', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO)->toBeInstanceOf(ValidatedDTO::class)
        ->and($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('throws exception when trying to instantiate a ValidatedDTO with invalid data')
    ->expect(fn () => new ValidatedDTOInstance([]))
    ->throws(ValidationException::class);

it('instantiates a ValidatedDTO with nullable and optional properties', function () {
    $dto = new NullableDTO([
        'name' => $this->subject_name,
        'address' => null,
    ]);

    expect($dto)->toBeInstanceOf(NullableDTO::class)
        ->and($dto->name)
        ->toBeString()
        ->and($dto->age)
        ->toBeNull()
        ->and($dto->address)
        ->toBeNull();
});

it('returns null when trying to access a property that does not exist', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO->age)->toBeNull();
});

it('validates that is possible to set a property in a ValidatedDTO', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    $validatedDTO->age = 30;

    expect($validatedDTO->age)->toBe(30);
});

it('validates that a ValidatedDTO can be instantiated from a JSON string', function () {
    $validatedDTO = ValidatedDTOInstance::fromJson('{"name": "'.$this->subject_name.'"}');

    expect($validatedDTO->validatedData)
        ->ToBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('throws exception when trying to instantiate a ValidatedDTO from an invalid JSON string')
    ->expect(fn () => ValidatedDTOInstance::fromJson('{"name": "'.$this->subject_name.'"'))
    ->throws(InvalidJsonException::class);

it('validates that a ValidatedDTO can be instantiated from Array', function () {
    $validatedDTO = ValidatedDTOInstance::fromArray(['name' => $this->subject_name]);

    expect($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that a ValidatedDTO can be instantiated from a Request', function () {
    $request = new Request(['name' => $this->subject_name]);

    $validatedDTO = ValidatedDTOInstance::fromRequest($request);

    expect($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that a ValidatedDTO can be instantiated from an Eloquent Model', function () {
    $model = new class() extends Model
    {
        protected $fillable = ['name'];
    };

    $model->fill(['name' => $this->subject_name]);

    $validatedDTO = ValidatedDTOInstance::fromModel($model);

    expect($validatedDTO->validatedData)
        ->toBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that a ValidatedDTO can be instantiated from Command arguments', function () {
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

    $validatedDTO = ValidatedDTOInstance::fromCommandArguments($command);

    expect($validatedDTO->validatedData)
        ->ToBe(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that a ValidatedDTO can be instantiated from Command options', function () {
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

    $validatedDTO = ValidatedDTOInstance::fromCommandOptions($command);

    expect($validatedDTO->validatedData)
        ->toEqual(['name' => $this->subject_name])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that a ValidatedDTO can be instantiated from a Command', function () {
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

    $validatedDTO = ValidatedDTOInstance::fromCommand($command);

    expect($validatedDTO->validatedData)
        ->toEqual(['name' => $this->subject_name, 'age' => 30])
        ->and($validatedDTO->validator->passes())
        ->toBeTrue();
});

it('validates that the ValidatedDTO can be converted into an array', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO)->toArray()
        ->toBe(['name' => $this->subject_name]);
});

it('validates that the ValidatedDTO can be converted into a JSON string', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO)->toJson()
        ->toBe('{"name":"'.$this->subject_name.'"}');
});

it('validates that the ValidatedDTO can be converted into a pretty JSON string with flag', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO)->toJson(true)
        ->toBe(json_encode(['name' => $this->subject_name], JSON_PRETTY_PRINT));
});

it('validates that the ValidatedDTO can be converted into a pretty JSON string', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    expect($validatedDTO)->toPrettyJson()
        ->toBe(json_encode(['name' => $this->subject_name], JSON_PRETTY_PRINT));
});

it('validates that the ValidatedDTO can be converted into an Eloquent Model', function () {
    $validatedDTO = new ValidatedDTOInstance(['name' => $this->subject_name]);

    $model = new class() extends Model
    {
        protected $fillable = ['name'];
    };

    $model_instance = $validatedDTO->toModel($model::class);

    expect($model_instance)
        ->toBeInstanceOf(Model::class)
        ->toArray()
        ->toBe(['name' => $this->subject_name]);
});

it('maps data before validation', function () {
    $dto = MapBeforeValidationDTO::fromArray(['full_name' => $this->subject_name]);

    expect($dto->full_name)
        ->toBeNull()
        ->and($dto->name)
        ->toBe($this->subject_name);
});

it('maps data before export', function () {
    $dto = MapBeforeExportDTO::fromArray(['name' => $this->subject_name]);

    expect($dto->name)
        ->toBe($this->subject_name)
        ->and($dto->username)
        ->toBeNull()
        ->and($dto->toArray())
        ->toBe(['username' => $this->subject_name]);
});

it('maps data before validation and before export', function () {
    $dto = MapDataDTO::fromArray(['full_name' => $this->subject_name]);

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
    $dto = MappedNameDTO::fromArray([
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
    $dto = UserDTO::fromArray([
        'name' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
        'email' => 'john.doe@example.com',
    ]);

    $user = $dto->toModel(User::class);

    expect($dto->name)
        ->toBeInstanceOf(NameDTO::class)
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
