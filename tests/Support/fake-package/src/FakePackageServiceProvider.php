<?php

namespace IBroStudio\FakePackage;

use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FakePackageServiceProvider extends PackageServiceProvider
{
    use UseNeonConfig;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('fake-package')
            ->hasConfigFile();
    }
}
