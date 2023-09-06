<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

it('generates a new ValidatedDTO class via command', function () {
    $dtoClass = app_path('DTOs/UserDTO.php');

    if (file_exists($dtoClass)) {
        unlink($dtoClass);
    }

    $this->artisan('make:dto', ['name' => 'UserDTO'])
        ->assertExitCode(0);

    expect($dtoClass)->toBeFileWithContent(UserDTO());
});

it('generates a new SimpleDTO class via command', function () {
    $dtoClass = app_path('DTOs/SimpleUserDTO.php');

    if (file_exists($dtoClass)) {
        unlink($dtoClass);
    }

    $this->artisan('make:dto', ['name' => 'SimpleUserDTO', '--simple' => true])
        ->assertExitCode(0);

    expect($dtoClass)->toBeFileWithContent(SimpleUserDTO());
});

it('generates a new ResourceDTO class via command', function () {
    $dtoClass = app_path('DTOs/UserResourceDTO.php');

    if (file_exists($dtoClass)) {
        unlink($dtoClass);
    }

    $this->artisan('make:dto', ['name' => 'UserResourceDTO', '--resource' => true])
        ->assertExitCode(0);

    expect($dtoClass)->toBeFileWithContent(UserResourceDTO());
});

it('generates DTO in custom namespace', function () {
    Config::set('dto.namespace', 'App\DataTransferObjects');

    $dtoClass = app_path('DataTransferObjects/UserDTO.php');

    if (file_exists($dtoClass)) {
        unlink($dtoClass);
    }

    $this->artisan('make:dto', ['name' => 'UserDTO'])
        ->assertExitCode(0);

    expect($dtoClass)->toBeFileWithContent(UserDTO('App\DataTransferObjects'));
});

/**
 * Content of the expected UserDTO class
 */
function UserDTO(string $namespace = 'App\DTOs'): string
{
    return <<<CLASS
<?php

namespace {$namespace};

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserDTO extends ValidatedDTO
{
    protected function rules(): array
    {
        return [];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}

CLASS;
}

/**
 * Content of the expected SimpleUserDTO class
 */
function SimpleUserDTO(): string
{
    return <<<CLASS
<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\SimpleDTO;

class SimpleUserDTO extends SimpleDTO
{
    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}

CLASS;
}

/**
 * Content of the expected UserResourceDTO class
 */
function UserResourceDTO(): string
{
    return <<<CLASS
<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\ResourceDTO;

class UserResourceDTO extends ResourceDTO
{
    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}

CLASS;
}
