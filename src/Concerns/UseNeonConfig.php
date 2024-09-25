<?php

namespace IBroStudio\NeonConfig\Concerns;

use Exception;
use IBroStudio\NeonConfig\Facades\NeonConfig;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Spatie\LaravelPackageTools\PackageServiceProvider;

trait UseNeonConfig
{
    public readonly ?\IBroStudio\NeonConfig\NeonConfig $neonConfig;

    public function handleNeon(?string $neonFile = null, ?string $directory = null): self
    {
        if (is_null($neonFile)) {

            if (! is_subclass_of($this, PackageServiceProvider::class)) {
                throw new Exception('Neon file is not provided.');
            }

            $neonFile = $this->package->shortName();
        }

        $this->neonConfig =
            NeonConfig::handleNeon($neonFile, $directory, $this->getNeonConfigNeonPath());

        return $this;
    }

    public function forConfig(?string $laravelConfigKey = null): bool
    {
        if (is_null($this->neonConfig)) {
            return false;
        }

        if (is_null($laravelConfigKey)) {

            if (! is_subclass_of($this, PackageServiceProvider::class)) {
                throw new Exception('Config key is not provided.');
            }

            $laravelConfigKey = $this->package->shortName();
        }

        return $this->neonConfig->forLaravelConfig($laravelConfigKey);
    }

    protected function getNeonConfigNeonPath(): string
    {
        return Str::of('/../')
            ->when(is_subclass_of($this, PackageServiceProvider::class),
                function (Stringable $string) {
                    return $string->prepend($this->package->basePath);
                },
                function (Stringable $string) {
                    $reflection = new ReflectionClass($this);

                    return $string->prepend(dirname($reflection->getFileName()));
                });
    }
}
