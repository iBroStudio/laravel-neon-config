<?php

namespace IBroStudio\NeonConfig;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use IBroStudio\NeonConfig\Commands\NeonConfigCommand;

class NeonConfigServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-neon-config')
            ->hasConfigFile()
            ->hasCommand(NeonConfigCommand::class);
    }
}
