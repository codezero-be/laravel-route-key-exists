# Laravel Route Key Exists

[![GitHub release](https://img.shields.io/github/release/codezero-be/laravel-route-key-exists.svg)]()
[![License](https://img.shields.io/packagist/l/codezero/laravel-route-key-exists.svg)]()
[![Build Status](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/badges/build.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-route-key-exists/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/laravel-route-key-exists.svg)](https://packagist.org/packages/codezero/laravel-route-key-exists)

#### Laravel validation rule to check if a custom route key exists.

Laravel's `exists` rule checks a database table for a column with a given value. This validation rule uses the `resolveRouteBinding()` method on a model to check if a given value exists.

## Requirements

-   PHP >= 7.0
-   [Laravel](https://laravel.com/) >= 5.5

## Installation

Require the package via Composer:

```
composer require codezero/laravel-route-key-exists
```
## Some Background Info

Laravel's [implicit route model binding](https://laravel.com/docs/5.5/routing#route-model-binding) allows you to automatically resolve a model by type hinting it in a controller. Furthermore, you can change the route key that is used to query the database in your model:

```php
public function getRouteKeyName()
{
    return 'id';
}
```

This also works when you are using a custom or computed route key in your model:

```php
public function getRouteKey()
{
    // "encode" the route key
    return "foo-{$this->id}";
}

public function resolveRouteBinding($value)
{
    // "decode" the route key
    $id = (int) str_replace('foo-', '', $value);

    // resolve from the database
    return $this->where('id', $id)->first();
}
```

But what if you are sending a custom key in a POST request and you want to validate it? Unlike Laravel's [`exists`](https://laravel.com/docs/5.5/validation#rule-exists) rule, this validation rule uses the `resolveRouteBinding()` method to check if the key is valid.

## Usage

Let's say you have a model with an ID of `1`, but `getRouteKey()` returns the encoded value of `1234`.

In your validation rules, new up `\CodeZero\RouteKeyExists\RouteKeyExists` and pass the model's class name to the constructor:

```php
request()->validate([
    'model_id' => new RouteKeyExists(Model::class),
]);
```

Here, `model_id` is the encoded value, which will be resolved using the `resolveRouteBinding()` method on your model. If it can't be resolved, validation fails.

Possibly, you will need the actual ID to work with when validation passes. Tack on `replace()` to the rule and `model_id` will be updated to the actual ID:

```php
request()->validate([
    'model_id' => (new RouteKeyExists(Model::class))->replace(),
]);

$id = request('model_id');
```

Or maybe you want to keep the encoded ID in the request, but add the actual ID as well. Just tack on `add()` and specify an attribute name:

```php
request()->validate([
    'model_id' => (new RouteKeyExists(Model::class))->add('actual_id'),
]);

$id = request('actual_id');
```

Beware that `actual_id` is not included on the `$attributes` array, since it is not being validated.

## Useful Packages

-   This rule works perfectly with the [`laravel-optimus`](https://github.com/cybercog/laravel-optimus) model trait.

## Testing

```
vendor/bin/phpunit
```

## Security

If you discover any security related issues, please [e-mail me](mailto:ivan@codezero.be) instead of using the issue tracker.

## Changelog

See a list of important changes in the [changelog](https://github.com/codezero-be/laravel-route-key-exists/blob/master/CHANGELOG.md).

## License

The MIT License (MIT). Please see [License File](https://github.com/codezero-be/laravel-route-key-exists/blob/master/LICENSE.md) for more information.
