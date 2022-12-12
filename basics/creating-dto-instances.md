# Creating DTO Instances

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

### From Artisan Commands

You have three ways of creating a `DTO` instance from an `Artisan Command`:

#### From the Command Arguments

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user {name} {email} {password}';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommandArguments($this);
    }
}
```

#### From the Command Options

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user { --name= : The user name }
                                        { --email= : The user email }
                                        { --password= : The user password }';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommandOptions($this);
    }
}
```

#### From the Command Arguments and Options

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user {name}
                                        { --email= : The user email }
                                        { --password= : The user password }';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommand($this);
    }
}
```
