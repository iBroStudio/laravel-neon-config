<?php

namespace IBroStudio\NeonConfig;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NeonConfigServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-neon-config');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(NeonConfig::class, function ($app) {
            return new NeonConfig;
        });
    }
}
