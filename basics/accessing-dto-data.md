# Accessing DTO Data

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
