<?php

namespace IBroStudio\FakePackage;

use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use Illuminate\Support\ServiceProvider;

class FakePackageServiceProviderWithoutPackageServiceProvider extends ServiceProvider
{
    use UseNeonConfig;
}
