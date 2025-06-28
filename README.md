# Laravel Neon Config

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ibrostudio/laravel-neon-config.svg?style=flat-square)](https://packagist.org/packages/ibrostudio/laravel-neon-config)

## Introduction

Laravel Neon Config is a package that integrates [Neon](https://github.com/nette/neon) configuration files with Laravel's config system. Neon is a human-friendly data serialization language similar to YAML but with a focus on being more readable and concise.

### What is Neon?

Neon (Nette Object Notation) is a human-readable structured data format. It is similar to YAML but is more focused on being concise and readable. Neon is developed by the Nette Foundation and is used extensively in Nette Framework applications.

### Goal of this Package

The primary goal of this package is to provide a configuration mechanism that works outside the context of a Laravel application. This is particularly useful in scenarios such as:

- When developing a package that is used as a dev dependency of another package
- When you need an environment file for tests while developing a package
- When you want to allow users of your package to easily override configuration values without modifying your package's files

**This package allows you to use a .neon file to overwrite a Laravel config file.**

## Installation

You can install the package via composer:

```bash
composer require ibrostudio/laravel-neon-config
```

## Basic Usage

### In a Package Service Provider

Add the trait `UseNeonConfig` to your package service provider and define your "neon to config" mapping:

```php
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;

class PackageServiceProvider extends ServiceProvider
{
    use UseNeonConfig;
    
    public function register()
    {
        $this->handleNeon('package-neon-file')->forConfig('package-config-key');
    }
}
```

### With Spatie's Package Skeleton

If your package uses **PackageServiceProvider** from Spatie's [package-skeleton-laravel](https://github.com/spatie/package-skeleton-laravel) and you are only dealing with a single config file, you can omit the name parameters:

```php
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;

class PackageServiceProvider extends PackageServiceProvider
{
    use UseNeonConfig;
    
    public function packageRegistered(): void
    {
        $this->handleNeon()->forConfig();
    }
}
```

### How It Works

When a user of your package creates a .neon file at the root of their project with the same name as your package, the values from that file will be merged with your package's configuration.

For example, if your package has this configuration:

```php
// config/your-package.php
return [
    'key1' => 'value1',
    'key2' => 'value2',
    'array' => [
        'array_key1' => 'array_value1',
        'array_key2' => 'array_value2',
    ],
];
```

The user can create a file named `your-package.neon` at the root of their project:

```neon
key1: new value
array:
    - new_array_key: new_value
    - array_key2: new_array_value
```

**Values are merged by keys, which means:**
- Values with the same keys are replaced
- Omitted keys are kept
- New keys are added

## Advanced Features

### Casting and Dynamic Values

#### Enums

If your config uses PHP Enums, you can cast values from the Neon file to the appropriate enum type:

```php
// config/your-package.php
return [
    'key1' => SomeEnumClass::VALUE,
];
```

At the root of your package, create a file named `neon-config.neon`:

```neon
casts:
    - key1: \Namespace\SomeEnumClass
```

Then, the user can override the enum value in their Neon file:

```neon
key1: newValue
```

Now, calling `config('your-package.key1')` will return an instance of `\Namespace\SomeEnumClass::NEW_VALUE`.

#### Array of Enums

You can also cast arrays of values to arrays of enum instances:

```php
// config/your-package.php
return [
    'key1' => [
        SomeEnumClass::VALUE1,
        SomeEnumClass::VALUE2,
        SomeEnumClass::VALUE3,
    ],
];
```

In your `neon-config.neon`:

```neon
casts:
    - key1: AsEnumCollection::\Namespace\SomeEnumClass
```

And in the user's Neon file:

```neon
key1:
    - value4
    - value5
    - value6
```

#### Dynamic Values from Config or Environment

You can also reference other config values or environment variables:

```php
// config/your-package.php
return [
    'key1' => config('some.config', 'default value'),
    'key2' => env('MY_ENV_VAR', 'default value'),
];
```

In your `neon-config.neon`:

```neon
casts:
    - key1: AsConfig::some.config
    - key2: AsEnv::MY_ENV_VAR
```

And in the user's Neon file:

```neon
key1: other.config.key(type: config, default: 'other default value')
key2: OTHER_ENV(type: env)
```

## Using as an Environment File for Tests

When developing a package, you can use Neon files as environment files for your tests:

### Configuration

Add the trait to your TestCase instead of the package service provider:

```php
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;

class TestCase extends Orchestra
{
    use UseNeonConfig;
    
    public function getEnvironmentSetUp($app)
    {
        $this->handleNeon('test')->forConfig('test');
    }
}
```

Create a `test.neon` file at the root of your package (add it to .gitignore):

```neon
key1: test-value-1
key2: test-value-2
```

During tests, you can now access these values with `config('test.key1')`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [iBroStudio](https://github.com/ibrostudio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
