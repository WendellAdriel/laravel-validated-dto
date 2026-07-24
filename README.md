<div align="center">
    <img src="https://github.com/wendelladriel/laravel-validated-dto/raw/main/art/laravel-validated-dto-banner.png" alt="Validated DTO for Laravel" height="300"/>
    <p>
        <h1>Validated DTO for Laravel</h1>
        Data Transfer Objects with validation for Laravel applications
    </p>
</div>

<p align="center">
    <a href="https://packagist.org/packages/wendelladriel/laravel-validated-dto"><img src="https://img.shields.io/packagist/v/wendelladriel/laravel-validated-dto.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/wendelladriel/laravel-validated-dto"><img src="https://img.shields.io/packagist/php-v/wendelladriel/laravel-validated-dto.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/wendelladriel/laravel-validated-dto"><img src="https://badge.laravel.cloud/badge/wendelladriel/laravel-validated-dto?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/wendelladriel/laravel-validated-dto/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/wendelladriel/laravel-validated-dto/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://laravel-validated-dto.wendelladriel.com"><img src="https://img.shields.io/badge/docs-website-blue?logo=readthedocs&style=flat-square" alt="Documentation"></a>
    <a href="https://packagist.org/packages/wendelladriel/laravel-validated-dto"><img src="https://img.shields.io/packagist/dt/wendelladriel/laravel-validated-dto.svg?style=flat-square" alt="Total Downloads"></a>
</p>

## Installation

You can install the package via composer:

```bash
composer require wendelladriel/laravel-validated-dto
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="validated-dto"
```

## Usage

Create a DTO by extending `ValidatedDTO`, declaring typed properties, and defining the validation rules for incoming data:

```php
use Illuminate\Validation\Rules\Password;
use WendellAdriel\ValidatedDTO\Casting\BooleanCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

final class UserDTO extends ValidatedDTO
{
    public string $name;

    public string $email;

    public string $password;

    public bool $active;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'active' => true,
        ];
    }

    protected function casts(): array
    {
        return [
            'name' => new StringCast(),
            'email' => new StringCast(),
            'password' => new StringCast(),
            'active' => new BooleanCast(),
        ];
    }
}
```

Create an instance from an array, request, JSON string, Eloquent model, or artisan command:

```php
$dto = UserDTO::fromArray([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->name; // John Doe
$dto->toArray();
```

You can also inject a DTO into a controller action and let the service container resolve it from the current request:

```php
use Illuminate\Http\JsonResponse;

final class StoreUserController
{
    public function __invoke(UserDTO $dto): JsonResponse
    {
        return response()->json($dto->toArray());
    }
}
```

Access the full documentation [here](https://laravel-validated-dto.wendelladriel.com).

## Changelog

Please see the [changelog](https://laravel-validated-dto.wendelladriel.com/getting-started/changelog) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Validated DTO for Laravel! You can read the contribution guide [here](CONTRIBUTING.md).

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## License

Validated DTO for Laravel is open-sourced software licensed under the [MIT license](LICENSE).
