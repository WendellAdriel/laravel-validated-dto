# Laravel Validated DTO

> Data Transfer Objects with validation for Laravel applications

## Installation

```
composer require wendelladriel/laravel-validated-dto
```

## Why to use this package

**Data Transfer Objects (DTOs)** are objects that are used to transfer data between systems. **DTOs** are typically used in applications to provide a simple, consistent format for transferring data between different parts of the application, such as **between the user interface and the business logic**.

This package provides a base **DTO Class** that can **validate** the data when creating a **DTO**. But why should we do this instead of using the standard **Request** validation?

Imagine that now you want to do the same action that you do in an endpoint on a **CLI** command per example. If your validation is linked to the Request you'll have to implement the same validation again.

With this package you **define the validation once** and can **reuse it where you need**, making your application more **maintainable** and **decoupled**.

## Generating DTOs

You can create `DTOs` using the `make:dto` command:

```
php artisan make:dto UserDTO
```

The `DTOs` are going to be created inside `app/DTOs`.

## Defining Validation Rules

You can validate data in the same way you validate `Request` data:

```php
<?php

namespace App\DTOs;

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
}
```

## Creating DTO instances

You can create a `DTO` instance on many ways:

### From arrays

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);
```

### From JSON strings

```php
$dto = UserDTO::fromJson('{"name": "John Doe", "email": "john.doe@example.com", "password": "s3CreT!@1a2B"}');
```

### From Request objects

```php
public function store(Request $request): JsonResponse
{
    $dto = UserDTO::fromRequest($request);
}
```

### From Eloquent Models

```php
$user = new User([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto = UserDTO::fromModel($user);
```

Beware that the fields in the `$hidden` property of the `Model` won't be used for the `DTO`.

## Accessing DTO data

After you create your `DTO` instance, you can access any properties like an `object`:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->name; // 'John Doe'
$dto->email; // 'john.doe@example.com'
$dto->password; // 's3CreT!@1a2B'
```

If you pass properties that are not listed in the `rules` method of your `DTO`, this data will be ignored and won't be available in your `DTO`:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
    'username' => 'john_doe', 
]);

$dto->username; // THIS WON'T BE AVAILABLE IN YOUR DTO
```

## Converting DTO data

You can convert your DTO to some formats:

### To array

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toArray();
// [
//     "name" => "John Doe",
//     "email" => "john.doe@example.com",
//     "password" => "s3CreT!@1a2B",
// ]
```

### To JSON string

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toJson();
// '{"name":"John Doe","email":"john.doe@example.com","password":"s3CreT!@1a2B"}'

$dto->toJson(true); // YOU CAN CALL IT LIKE THIS TO PRETTY PRINT YOUR JSON
// {
//     "name": "John Doe",
//     "email": "john.doe@example.com",
//     "password": "s3CreT!@1a2B"
// }
```

### To Eloquent Model

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toModel(\App\Models\User::class);
// App\Models\User {#3776
//     name: "John Doe",
//     email: "john.doe@example.com",
//     password: "s3CreT!@1a2B",
// }

```

## Customizing Error Messages, Attributes and Exceptions

You can define custom messages and attributes implementing the `messages` and `attributes` methods:

```php
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
```

You can define custom `Exceptions` implementing the `failedValidation` method:

```php
/**
 * Handles a failed validation attempt.
 *
 * @return void
 *
 * @throws ValidationException
 */
protected function failedValidation(): void
{
    throw new ValidationException($this->validator);
}
```

## TO DO

- Create tests

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## Contributing

All PRs are welcome.

For major changes, please open an issue first describing what you want to add/change.
