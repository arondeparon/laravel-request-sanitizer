# Laravel Request Sanitizer

[![Build Status](https://travis-ci.org/ArondeParon/laravel-request-sanitizer.svg?branch=master)](https://travis-ci.org/ArondeParon/laravel-request-sanitizer)

The `arondeparon/laravel-request-sanitizer` package provides a fluent interface to sanitize form requests before validating them.

## Why should I use this package?

Often, validating your request is not enough. The request sanitizer allows you to easily 
sanitize your form data before passing it to the validator. You can start using it in a matter
of minutes and it is fully compatible with Laravel's `FormRequest` object.

## How to use

Syntax is similar to the way `rules` are added to a Form Request.

```php
class StoreCustomerInformationRequest extends FormRequest
{
     use SanitizesInputs;
     
     protected $sanitizers = [
        'lastname' => [
            Capitalize::class,
        ],
        'mobile_phone' => [
            RemoveNonNumeric::class
        ],
     ];
}
```

## Installing

`composer require arondeparon/laravel-request-sanitizer`

## Usage

- Add the `SanitizesInputs` trait to your form request.
- Write your own sanitizers or use one of the supplied sanitizers and add them to the `$sanitizers`
property of your form request.
- Your request data will not be sanitized before being validated.

## Predefined Sanitizers

- [`RemoveNonNumeric`](./src/Sanitizers/RemoveNonNumeric.php) - removes any non numeric character
- [`Trim`](./src/Sanitizers/Trim.php) - simple PHP `trim()` implementation
- [`TrimDuplicateSpaces`](./src/Sanitizers/TrimDuplicateSpaces.php) replaces duplicate spaces with a single space.
- ...
- Contributions are appreciated!

## Writing your own Sanitizers

Writing your own sanitizer can be done by implementing the `Sanitizer` interface, which requires only
one method.

```php
interface Sanitizer
 {
     public function sanitize($input);
 }
```

## Testing

`$ phpunit`

## Credits

- [Aron Rotteveel](https://github.com/arondeparon)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
