<?php

declare(strict_types=1);

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

/**
 * Content of the expected UserDTO class
 */
function UserDTO(): string
{
    return <<<CLASS
<?php

namespace App\DTOs;

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
