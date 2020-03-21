# Laravel Request Sanitizer

[![Build Status](https://travis-ci.org/ArondeParon/laravel-request-sanitizer.svg?branch=master)](https://travis-ci.org/ArondeParon/laravel-request-sanitizer)
[![Total Downloads][ico-downloads]][link-downloads]

The `arondeparon/laravel-request-sanitizer` package provides a fluent interface to sanitize form requests before validating them.

## Why should I use this package?

Often, validating your request is not enough. The request sanitizer allows you to easily 
manipulate your form data before passing it to the validator. You can start using it in a matter
of minutes and it is fully compatible with Laravel's `FormRequest` object.

## How to use

Syntax is similar to the way `rules` are added to a [Form Request](https://laravel.com/docs/master/validation#form-request-validation).

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
- Your request data will now be sanitized before being validated.

## Predefined Sanitizers

- [`Trim`](./src/Sanitizers/Trim.php) - simple PHP `trim()` implementation
- [`TrimDuplicateSpaces`](./src/Sanitizers/TrimDuplicateSpaces.php) replaces duplicate spaces with a single space.
- [`RemoveNonNumeric`](./src/Sanitizers/RemoveNonNumeric.php) - removes any non numeric character
- [`Capitalize`](./src/Sanitizers/Capitalize.php) - capitalizes the first character of a string
- [`Uppercase`](./src/Sanitizers/Uppercase.php) - converts a string to uppercase
- [`Lowercase`](./src/Sanitizers/Lowercase.php) - converts a string to lowercasse
- [`Strip`](./src/Sanitizers/Strip.php) - strips html tags
- Contributions are appreciated!

## Default Sanitizers

You can define default sanitizers which will be applied to each request input property (rules array).

First publish the sanitizer config: <br>
`php artisan vendor:publish --provider="ArondeParon\RequestSanitizer\RequestSanitizerServiceProvider" --tag="config"`

Activate the sanitizer:
```php
return [
     'defaults' => [
         \ArondeParon\RequestSanitizer\Sanitizers\Strip::class
     ]
 ];
```

If you want to omit the default handling for a specific request, just set the following property on the request class:
```php
protected $useDefaults = false;
```


## Writing your own Sanitizer

Writing your own sanitizer can be done by implementing the `Sanitizer` interface, which requires only
one method.

```php
interface Sanitizer
 {
     public function sanitize($input);
 }
```



## Testing

`$ composer test`

## Credits

- [Aron Rotteveel](https://github.com/arondeparon)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-downloads]: https://packagist.org/packages/arondeparon/laravel-request-sanitizer
[ico-downloads]: https://img.shields.io/packagist/dt/arondeparon/laravel-request-sanitizer.svg?style=flat-square
