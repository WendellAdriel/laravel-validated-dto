---
name: laravel-validated-dto-development
description: Guidance for generating, designing, and refactoring ValidatedDTO, SimpleDTO, and ResourceDTO classes in Laravel applications.
metadata:
---

# Laravel Validated DTO

Use this skill when working with `wendelladriel/laravel-validated-dto`: generating DTO classes, improving existing DTOs, applying attributes and casts, or replacing `FormRequest` validation with DTOs.

## Choose the right DTO type

- `ValidatedDTO`: use when incoming data must be validated before the DTO is considered ready.
- `SimpleDTO`: use when you want typed data, casts, mapping, and transforms without validation.
- `ResourceDTO`: use when the DTO is primarily an API response object and should be returned directly from controllers.

Default to `ValidatedDTO` unless the code clearly does not need validation or is strictly response-only.

## Generate DTOs with commands

Use the package commands instead of hand-writing boilerplate first:

```bash
php artisan make:dto UserDTO
php artisan make:dto CheckoutInputDTO --simple
php artisan make:dto UserResourceDTO --resource
```

- Generated classes go to `App\DTOs` by default.
- The namespace comes from `config/dto.php` via `dto.namespace`.
- If the project needs custom templates, publish the stubs first:

```bash
php artisan dto:stubs
```

That creates customizable stubs in `stubs/` for validated, simple, and resource DTOs.

## Authoring high-quality DTOs

### Core rules

- Declare public typed properties for every field the DTO owns.
- Keep validation rules, default values, casts, and mapping aligned with those property types.
- Prefer explicit casts for non-trivial types, nested objects, collections, enums, dates, and numeric string inputs.
- Use `nullable` or `optional` rules when a property can legitimately become `null`.
- Keep DTOs focused on transport and normalization, not business logic.

### Required methods

For generated DTOs, these are the main hooks:

- `rules()`: validation rules for `ValidatedDTO` only.
- `defaults()`: fallback values when data is missing.
- `casts()`: property casting definitions.

Add these only when needed:

- `messages()`: custom validation messages.
- `attributes()`: human-friendly attribute names.
- `mapData()`: map external input keys before validation/assignment.
- `mapToTransform()`: map DTO keys before `toArray()`, `toJson()`, or `toModel()`.

## Prefer attributes for concise DTOs

Use attributes when rules, casts, defaults, or mapping are property-local and easy to read inline.

### Available attributes

- `#[Rules([...], messages: [...])]`: define validation rules per property.
- `#[DefaultValue(...)]`: define fallback values.
- `#[Cast(Type::class, param: OtherType::class)]`: define casts inline.
- `#[Map(data: 'incoming_key', transform: 'outgoing_key')]`: map input/output names.
- `#[Receive(PropertyCase::SnakeCase)]`: accept a naming convention for all incoming properties.
- `#[Provide(PropertyCase::PascalCase)]`: transform all outgoing properties to a naming convention.
- `#[Lazy]`: defer validation and casting until `validate()` is called.
- `#[SkipOnTransform]`: exclude a property from `toArray()`, `toJson()`, and `toModel()`.

### Traits that pair well with attributes

If a DTO is fully attribute-driven, use these traits so the class can stay minimal:

- `EmptyRules`
- `EmptyDefaults`
- `EmptyCasts`

This pattern is especially useful for attribute-heavy `ValidatedDTO` classes.

## Casting guidance

Use built-in casts whenever possible:

- scalars: `StringCast`, `IntegerCast`, `FloatCast`, `BooleanCast`
- structures: `ArrayCast`, `CollectionCast`, `ObjectCast`
- dates: `CarbonCast`, `CarbonImmutableCast`
- domain types: `DTOCast`, `ModelCast`, `EnumCast`

Strong defaults:

- Use `DTOCast` for nested DTO objects.
- Use `CollectionCast(new DTOCast(...))` or `ArrayCast(new DTOCast(...))` for nested lists.
- Use `EnumCast` for PHP enums instead of manual string handling.
- Use a custom `Castable` implementation or callable cast when the transformation is project-specific.

If `config('dto.require_casting')` is `true`, every DTO property must have a cast or instantiation will fail.

## Mapping guidance

Use method-based mapping for complex or nested remapping, and attributes for simple one-property renames.

- `mapData()`: input normalization before validation.
- `mapToTransform()`: output normalization before array/json/model export.
- `#[Receive(...)]` and `#[Provide(...)]`: bulk casing conversion for all properties.
- `#[SkipOnTransform]`: keep internal-only fields out of exported payloads.

Use nested mapping when the external payload shape and the internal DTO shape intentionally differ.

## Working with runtime sources

DTOs can be created from several sources:

```php
$dto = UserDTO::fromArray($data);
$dto = UserDTO::fromJson($json);
$dto = UserDTO::fromRequest($request);
$dto = UserDTO::fromModel($user);
$dto = UserDTO::fromCommandArguments($this);
$dto = UserDTO::fromCommandOptions($this);
$dto = UserDTO::fromCommand($this);
```

For controllers in Laravel, prefer type-hinting the DTO directly when the package is already wired into the app:

```php
public function store(UserDTO $dto)
{
    // ...
}
```

## Livewire and lazy validation

When a DTO is filled progressively, prefer lazy validation:

- set `public bool $lazyValidation = true`, or
- add `#[Lazy]` to the DTO class.

Call `$dto->validate()` when the DTO is ready.

For Livewire integration, use the `Wireable` trait so the DTO can move between Livewire and PHP safely.

## Converting FormRequests into DTOs

When asked to convert a `FormRequest` into a DTO, use this migration approach:

1. Create a `ValidatedDTO` with public typed properties matching the validated payload.
2. Move `rules()` from the `FormRequest` into the DTO.
3. Move `messages()` and `attributes()` if the request defines them.
4. Convert `prepareForValidation()` style key normalization into `mapData()` or `#[Map(...)]` when possible.
5. Convert output reshaping into `mapToTransform()` or `#[Map(transform: ...)]` when needed.
6. Add casts for booleans, integers, floats, enums, nested DTOs, collections, models, and dates.
7. Replace controller signatures like `store(StoreUserRequest $request)` with `store(StoreUserDTO $dto)` when the app is using DTO auto-resolution.
8. Replace usages of `$request->validated()` with DTO properties.

Important differences from `FormRequest`:

- Authorization does not live in the DTO; keep that in policies, middleware, or controller/application logic.
- DTOs are reusable outside HTTP, so prefer keeping request-only concerns out of them.
- File validation rules can stay in the DTO; uploaded files are supported.

## Refactoring checklist

When creating or updating a DTO, verify that:

- property types, rules, defaults, and casts agree with each other
- nullable fields have matching validation rules
- nested data uses `DTOCast`, `CollectionCast`, or `ArrayCast` correctly
- mapping is symmetrical only when it needs to be
- exported payloads do not leak internal-only properties
- the chosen DTO base class matches the actual use case

## Good defaults to follow

- Prefer attributes for local property concerns.
- Prefer methods for cross-field or nested mapping.
- Prefer explicit casts over relying on raw input types.
- Prefer `ResourceDTO` only for response objects.
- Prefer DTO injection over manually duplicating request validation in controllers.
