<?php

use IBroStudio\FakePackage\FakePackageServiceProvider;
use IBroStudio\FakePackage\FakePackageServiceProviderWithoutPackageServiceProvider;
use IBroStudio\NeonConfig\NeonConfig;

it('can handle neon file from name', function () {
    $package = $this->app->getProvider(FakePackageServiceProvider::class);
    $package->handleNeon(
        neonFile: 'fake-package',
        directory: __DIR__.'/../Support/fake-end-package'
    );

    expect($package->neonConfig)
        ->toBeInstanceOf(NeonConfig::class);
});

it('can handle neon file from filename', function () {
    $package = $this->app->getProvider(FakePackageServiceProvider::class);
    $package->handleNeon(
        neonFile: 'fake-package.neon',
        directory: __DIR__.'/../Support/fake-end-package'
    );

    expect($package->neonConfig)
        ->toBeInstanceOf(NeonConfig::class);
});

it('can guess neon file for package using PackageServiceProvider', function () {
    $package = $this->app->getProvider(FakePackageServiceProvider::class);
    $package->handleNeon(directory: __DIR__.'/../Support/fake-end-package');

    expect($package->neonConfig)
        ->toBeInstanceOf(NeonConfig::class);
});

it('can not guess neon file without PackageServiceProvider', function () {
    $package = $this->app->getProvider(FakePackageServiceProviderWithoutPackageServiceProvider::class);
    $package->handleNeon(directory: __DIR__.'/../Support/fake-end-package');
})->throws(Exception::class, 'Neon file is not provided.');

it('can handle config key', function () {
    $package = $this->app->getProvider(FakePackageServiceProvider::class);

    expect(
        $package->handleNeon(
            neonFile: 'fake-package',
            directory: __DIR__.'/../Support/fake-end-package'
        )->forConfig('fake-package')
    )
        ->toBeTrue();
});

it('can guess config key for package using PackageServiceProvider', function () {
    $package = $this->app->getProvider(FakePackageServiceProvider::class);

    expect(
        $package->handleNeon(
            neonFile: 'fake-package',
            directory: __DIR__.'/../Support/fake-end-package'
        )->forConfig()
    )
        ->toBeTrue();
});

it('can not guess config key without PackageServiceProvider', function () {
    $package = $this->app->getProvider(FakePackageServiceProviderWithoutPackageServiceProvider::class);
    $package->handleNeon(
        neonFile: 'fake-package',
        directory: __DIR__.'/../Support/fake-end-package'
    )->forConfig();
})->throws(Exception::class, 'Config key is not provided.');
