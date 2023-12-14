<?php

declare(strict_types=1);

it('publishes the package stubs', function () {
    $this->artisan('dto:stubs')
        ->assertExitCode(0);

    expect(base_path('stubs/resource_dto.stub'))->toBeFile();
    expect(base_path('stubs/simple_dto.stub'))->toBeFile();
    expect(base_path('stubs/dto.stub'))->toBeFile();
});

it('publishes the package stubs with force flag', function () {
    $this->artisan('dto:stubs', ['--force' => true])
        ->assertExitCode(0);

    expect(base_path('stubs/resource_dto.stub'))->toBeFile();
    expect(base_path('stubs/simple_dto.stub'))->toBeFile();
    expect(base_path('stubs/dto.stub'))->toBeFile();

    expect(base_path('stubs/dto.stub'))->toBeFileWithContent(UserStubDTO());
    expect(base_path('stubs/simple_dto.stub'))->toBeFileWithContent(SimpleUserStubDTO());
    expect(base_path('stubs/resource_dto.stub'))->toBeFileWithContent(UserResourceStubDTO());
});

/**
 * Content of the expected UserDTO class
 */
function UserStubDTO(): string
{
    return <<<CLASS
<?php

namespace {{ namespace }};

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class {{ class }} extends ValidatedDTO
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
function SimpleUserStubDTO(): string
{
    return <<<CLASS
<?php

namespace {{ namespace }};

use WendellAdriel\ValidatedDTO\SimpleDTO;

class {{ class }} extends SimpleDTO
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
function UserResourceStubDTO(): string
{
    return <<<CLASS
<?php

namespace {{ namespace }};

use WendellAdriel\ValidatedDTO\ResourceDTO;

class {{ class }} extends ResourceDTO
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
