# Custom Exceptions

You can define custom `Exceptions` by implementing the `failedValidation` method:

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
