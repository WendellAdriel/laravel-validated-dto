<?php

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
    /**
     * Defines the validation rules for the DTO.
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Defines the default values for the properties of the DTO.
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Defines the type casting for the properties of the DTO.
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO instantiation.
     */
    protected function mapBeforeValidation(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO export.
     */
    protected function mapBeforeExport(): array
    {
        return [];
    }

    /**
     * Defines the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Defines the custom attributes for validator errors.
     */
    public function attributes(): array
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
    /**
     * Defines the default values for the properties of the DTO.
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Defines the type casting for the properties of the DTO.
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO instantiation.
     */
    protected function mapBeforeValidation(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO export.
     */
    protected function mapBeforeExport(): array
    {
        return [];
    }
}

CLASS;
}
