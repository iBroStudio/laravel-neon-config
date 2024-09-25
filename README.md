# Laravel Neon Config

[Neon](https://github.com/nette/neon) config management for Laravel packages.

The goal of this package is to provide a config file available outside the context of a Laravel app.

For example : a package used as a dev dependency of another package.

**This package allows you to use a .neon file to overwrite a Laravel config file.**

## Installation

```bash
composer require ibrostudio/laravel-neon-config
```

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
...
```

If your package uses **PackageServiceProvider** from Spatie's [package-skeleton-laravel ](https://github.com/spatie/package-skeleton-laravel) and you are only dealing with a single config file, you can omit the name parameters:
```php
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;

class PackageServiceProvider extends PackageServiceProvider
{
    use UseNeonConfig;
    
    public function packageRegistered(): void
    {
        $this->handleNeon()->forConfig();
    }
...
```

## Usage

Creating a .neon file at the root of the package that uses yours overwrites your package configuration.

config/your-package.php:
```php
return [
    'key1' => 'value1',
    'key2' => 'value2',
    'array' => [
        'array_key1' => 'array_value1',
        'array_key2' => 'array_value2',
    ],
];
```

your-package.neon:
```neon
key1: new value
array:
    - new_array_key: new_value
    - array_key2: array_value
```

**Values are merged by keys, which means : values with the same keys are replaces, omitted keys are kept, new keys are added.**


## Casting and dynamic values

### Enums
If your config uses Enums, it is possible to reflect and cast it in the neon file.

config/your-package.php:
```php
return [
    'key1' => SomeEnumClass::VALUE,
];
```

At the root of your package, create a file named neon-config.neon:

neon-config.neon:
```neon
casts:
    - key1: \Namespace\SomeEnumClass
```

Then, the overwrite is written in plain text with the value of the enum to use:
your-package.neon:
```neon
key1: newValue
```

Now, calling `config('your-package.key1')` gives an enum \Namespace\SomeEnumClass::NEW_VALUE instance.


### Array of Enums
config/your-package.php:
```php
return [
    'key1' => [
        SomeEnumClass::VALUE1,
        SomeEnumClass::VALUE2,
        SomeEnumClass::VALUE3,
    ],
];
```

neon-config.neon:
```neon
casts:
    - key1: AsEnumCollection::\Namespace\SomeEnumClass
```

your-package.neon:
```neon
key1:
    - value4
    - value5
    - value6
```

### Dynamic values from other config or env variables

config/your-package.php:
```php
return [
    'key1' => config('some.config', 'default value'),
    'key2' => env('MY_ENV_VAR', 'default value' ),
];
```

It is possible to keep the usage of the config() and env() methods and give the possibility of overwriting their keys:

neon-config.neon:
```neon
casts:
    - key1: AsConfig::some.config
    - key2: AsEnv::MY_ENV_VAR
```

your-package.neon:
```neon
key1: other.config.key(type: config, default: 'other default value')
key2: OTHER_ENV(type: env)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
