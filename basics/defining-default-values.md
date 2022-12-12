# Defining Default Values

Sometimes we can have properties that are optional and that can have default values. You can define the default values for your `DTO` properties in the `defaults` function:

```php
<?php

namespace App\DTOs;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserDTO extends ValidatedDTO
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email'],
            'username' => ['sometimes', 'string'],
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }
    
    /**
     * Defines the default values for the properties of the DTO.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'username' => Str::snake($this->name),
        ];
    }
}
```

With the `DTO` definition above you could run:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->username; // 'john_doe'
```
