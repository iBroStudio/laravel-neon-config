<?php

namespace IBroStudio\NeonConfig\Tests;

use IBroStudio\FakeEndPackage\FakeEndPackageServiceProvider;
use IBroStudio\FakePackage\FakePackageServiceProvider;
use IBroStudio\FakePackage\FakePackageServiceProviderWithoutPackageServiceProvider;
use IBroStudio\NeonConfig\NeonConfigServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IBroStudio\\NeonConfig\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            NeonConfigServiceProvider::class,
            FakePackageServiceProvider::class,
            FakePackageServiceProviderWithoutPackageServiceProvider::class,
            FakeEndPackageServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-neon-config_table.php.stub';
        $migration->up();
        */
    }
}
