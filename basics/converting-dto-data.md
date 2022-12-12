# Converting DTO Data

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
