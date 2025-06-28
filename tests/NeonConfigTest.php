<?php

use IBroStudio\NeonConfig\NeonConfig;
use IBroStudio\NeonConfig\Tests\Support\FakeEnum;
use Nette\Neon\Neon;

it('can handle neon file', function () {
    expect((new NeonConfig)->handleNeon('valid.neon', __DIR__.'/Support'))
        ->toBeInstanceOf(NeonConfig::class);
});

it('can configure NeonConfig', function () {
    $neonConfig = new NeonConfig;
    $neonConfig->handleNeon('valid.neon', __DIR__.'/Support', __DIR__.'/Support');

    expect($neonConfig->getCasts())
        ->toMatchArray([
            'enum' => '\IBroStudio\NeonConfig\Tests\Support\FakeEnum',
            'collection' => 'AsEnumCollection::\IBroStudio\NeonConfig\Tests\Support\FakeEnum',
            'name' => 'AsConfig::app.name',
            'url' => 'AsConfig::unknown',
            'environment' => 'AsEnv::APP_ENV',
            'key' => 'AsEnv::UNKNOWN',
        ]);
});

it('can load and merge neon config in Laravel', function () {

    expect(
        (new NeonConfig)
            ->handleNeon('valid.neon', __DIR__.'/Support')
            ->forLaravelConfig('neon-config')
    )
        ->toBeTrue()
        ->and(config('neon-config'))
        ->toMatchArray([
            'street' => '742 Evergreen Terrace',
            'city' => 'Springfield',
            'country' => 'USA',
        ]);
});

it('throws error on invalid neon file', function () {
    (new NeonConfig)
        ->handleNeon('invalid.neon', __DIR__.'/Support')
        ->forLaravelConfig('neon-config');
})->throws(Exception::class, __DIR__.'/Support/invalid.neon is not compatible with Laravel.');

it('can cast to enum', function () {
    (new NeonConfig)
        ->handleNeon('withEnum.neon', __DIR__.'/Support', __DIR__.'/Support')
        ->forLaravelConfig('neon-test');

    $enum = config('neon-test.enum');

    expect($enum)->toBeInstanceOf(FakeEnum::class)
        ->and($enum->value)->toBe('test');
});

it('can cast to enum collection', function () {
    (new NeonConfig)
        ->handleNeon('withEnumCollection.neon', __DIR__.'/Support', __DIR__.'/Support')
        ->forLaravelConfig('neon-test');

    $collection = config('neon-test.collection');

    expect($collection)->toMatchArray([
        FakeEnum::TEST,
        FakeEnum::TEST2,
        FakeEnum::TEST3,
    ]);
});

it('can cast to config', function () {
    (new NeonConfig)
        ->handleNeon('withConfig.neon', __DIR__.'/Support', __DIR__.'/Support')
        ->forLaravelConfig('neon-test');

    expect(config('neon-test.name'))->toBe(config('app.name'))
        ->and(config('neon-test.url'))->toBe('https://laravel.com');
});

it('can cast to env', function () {
    (new NeonConfig)
        ->handleNeon('withEnv.neon', __DIR__.'/Support', __DIR__.'/Support')
        ->forLaravelConfig('neon-test');

    expect(config('neon-test.environment'))->toBe('testing')
        ->and(config('neon-test.key'))->toBe(1234567890);
});

it('can generate neon file from Laravel config', function () {
    expect(
        Neon::decode(
            (new NeonConfig)->generateNeon(
                laravelConfigKey: 'fake-package',
                neonConfigNeonDirectory: __DIR__.'/Support',
            )
        )
    )->toMatchArray(Neon::decodeFile(__DIR__.'/Support/compare.neon'));
});
