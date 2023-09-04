<div align="center">
    <img src="https://github.com/WendellAdriel/laravel-validated-dto/raw/main/art/laravel-validated-dto-banner.png" alt="Validated DTO for Laravel" height="300"/>
    <p>
        <h1>Validated DTO for Laravel</h1>
        Data Transfer Objects with validation for Laravel applications
    </p>
</div>

<p align="center">
    <a href="https://packagist.org/packages/WendellAdriel/laravel-validated-dto"><img src="https://img.shields.io/packagist/v/WendellAdriel/laravel-validated-dto.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/WendellAdriel/laravel-validated-dto"><img src="https://img.shields.io/packagist/php-v/WendellAdriel/laravel-validated-dto.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/WendellAdriel/laravel-validated-dto"><img src="https://img.shields.io/badge/Laravel-9.x,%2010.x-brightgreen.svg?style=flat-square" alt="Laravel Version"></a>
    <a href="https://github.com/WendellAdriel/laravel-validated-dto/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/WendellAdriel/laravel-validated-dto/tests.yml?branch=main&label=Tests"> </a>
</p>

**Data Transfer Objects (DTOs)** are objects that are used to transfer data between systems. **DTOs** are typically used in applications to provide a simple, consistent format for transferring data between different parts of the application, such as **between the user interface and the business logic**.

This package provides a base **DTO Class** that can **validate** the data when creating a **DTO**. But why should we do this instead of using the standard **Request** validation?

Imagine that now you want to do the same action that you do in an endpoint on a **CLI** command per example. If your validation is linked to the Request you'll have to implement the same validation again.

With this package you **define the validation once** and can **reuse it where you need**, making your application more **maintainable** and **decoupled**.

## Documentation 
[![Docs Button]][Docs Link] [![DocsRepo Button]][DocsRepo Link]

## Installation

```bash
composer require wendelladriel/laravel-validated-dto
```

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## Contributing

Check the **[Contributing Guide](CONTRIBUTING.md)**.

<!---------------------------------------------------------------------------->
[Docs Button]: https://img.shields.io/badge/Website-0dB816?style=for-the-badge&logoColor=white&logo=GitBook
[Docs Link]: https://wendell-adriel.gitbook.io/laravel-validated-dto/
[DocsRepo Button]: https://img.shields.io/badge/Repository-3884FF?style=for-the-badge&logoColor=white&logo=GitBook
[DocsRepo Link]: https://github.com/WendellAdriel/laravel-validated-dto-docs
