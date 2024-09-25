<?php

namespace IBroStudio\NeonConfig;

use Exception;
use IBroStudio\NeonConfig\Concerns\UseCasts;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Nette\Neon\Entity;
use Nette\Neon\Neon;

final class NeonConfig
{
    use UseCasts;

    public const string NEON_EXT = '.neon';

    public const string NEON_CONFIG_FILE = 'neon-config.neon';

    protected string $neonFilePath;

    protected string $laravelConfig;

    protected array $valuesFromNeon = [];

    protected array $casts = [];

    /**
     * @throws \Nette\Neon\Exception
     */
    public function handleNeon(string $neonFile, ?string $directory = null, ?string $neonConfigNeonDirectory = null): ?self
    {
        $this->neonFilePath = $this->getNeonFilePath($neonFile, $directory);

        if (! File::exists($this->neonFilePath)) {
            return null;
        }

        if (! is_null($neonConfigNeonDirectory)) {
            $this->configure($neonConfigNeonDirectory);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function forLaravelConfig(string $laravelConfigKey): bool
    {
        $this->laravelConfig = $laravelConfigKey;

        return $this
            ->load()
            ->merge();
    }

    public function getNeonFilePath(string $neonFile, ?string $directory = null): string
    {
        return Str::of($neonFile)
            ->basename(self::NEON_EXT)
            ->slug()
            ->append(self::NEON_EXT)
            ->when(! is_null($directory),
                function (Stringable $string) use ($directory) {
                    return $string
                        ->when(! Str::endsWith($directory, '/'), function (Stringable $string) {
                            return $string->prepend('/');
                        })
                        ->prepend($directory);
                },
                function (Stringable $string) {
                    return $string->prepend(getcwd().'/');
                });
    }

    public function getCasts(): array
    {
        return $this->casts;
    }

    public function getValuesFromNeon(): array
    {
        return $this->valuesFromNeon;
    }

    public function generateNeon(string $laravelConfigKey, ?string $neonConfigNeonDirectory = null): string
    {
        $config = config($laravelConfigKey);

        if (! is_array($config)) {
            throw new Exception($laravelConfigKey.' is not a valid config key.');
        }

        if (! count($config)) {
            throw new Exception($laravelConfigKey.' is empty.');
        }

        if (! is_null($neonConfigNeonDirectory)) {
            $this->configure($neonConfigNeonDirectory);
        }

        foreach ($this->casts as $laravelConfigKey => $cast) {
            if (Arr::has($config, $laravelConfigKey)) {
                data_set(
                    target: $config,
                    key: $laravelConfigKey,
                    value: $this->cast(
                        laravelConfigKey: $laravelConfigKey,
                        value: data_get(target: $config, key: $laravelConfigKey)
                    )->from()
                );
            }
        }

        return Neon::encode($config, true);
    }

    /**
     * @throws \Nette\Neon\Exception
     */
    protected function configure(string $directory): void
    {
        if (File::exists($directory.'/'.self::NEON_CONFIG_FILE)) {

            $config = Neon::decodeFile($directory.'/'.self::NEON_CONFIG_FILE);

            $this->casts =
                collect($config['casts'])
                    ->mapWithKeys(function ($config) {
                        return collect($config)
                            ->mapWithKeys(function ($value, $key) {
                                return [
                                    $key => $value instanceof Entity ? $this->getCast($value) : $value,
                                ];
                            })->toArray();
                    })
                    ->toArray();
        }
    }

    /**
     * @throws \Nette\Neon\Exception
     */
    protected function load(): self
    {
        $decodedNeonValues = Neon::decodeFile($this->neonFilePath);

        foreach ($this->casts as $laravelConfigKey => $cast) {
            if (Arr::has($decodedNeonValues, $laravelConfigKey)) {
                data_set(
                    target: $decodedNeonValues,
                    key: $laravelConfigKey,
                    value: $this->cast(
                        laravelConfigKey: $laravelConfigKey,
                        value: data_get(target: $decodedNeonValues, key: $laravelConfigKey)
                    )->to()
                );
            }
        }

        if (is_array($decodedNeonValues)) {
            $this->valuesFromNeon = $decodedNeonValues;

            return $this;
        }

        throw new Exception($this->neonFilePath.' is not compatible with Laravel compatible.');
    }

    protected function merge(): bool
    {
        Config::set($this->laravelConfig, array_merge(
            Config::get($this->laravelConfig) ?? [],
            $this->valuesFromNeon
        ));

        return true;
    }
}

/*
 * hommebijoux_db
 * hommebijoux_dbu
 * z6PKAjIgdQVzrcmtE2YU
 *
 *
 */
