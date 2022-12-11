<?php

namespace WendellAdriel\ValidatedDTO\Tests\Feature;

use WendellAdriel\ValidatedDTO\Tests\TestCase;

class MakeDTOCommandTest extends TestCase
{
    public function testItCreatesANewValidatedDTOClass(): void
    {
        $dtoClass = app_path('DTOs/UserDTO.php');

        $this->artisan('make:dto', ['name' => 'UserDTO'])
            ->assertExitCode(0);

        $this->assertTrue(file_exists($dtoClass));

        $expectedContent = <<<CLASS
<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserDTO extends ValidatedDTO
{
    /**
     * Defines the validation rules for the DTO.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Defines the default values for the properties of the DTO.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Defines the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Defines the custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }
}

CLASS;

        $this->assertEquals($expectedContent, file_get_contents($dtoClass));
    }
}
