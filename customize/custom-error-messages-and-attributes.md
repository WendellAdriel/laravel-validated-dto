# Custom Error Messages and Attributes

You can define custom messages and attributes by implementing the `messages` and `attributes` methods:

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
