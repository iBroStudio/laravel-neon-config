<?php

namespace IBroStudio\NeonConfig\Casts;

use IBroStudio\NeonConfig\Concerns\HasEnumUtils;
use IBroStudio\NeonConfig\Contracts\NeonConfigCasterContract;

class AsEnumCollection
{
    public static function castUsing(array $arguments): NeonConfigCasterContract
    {
        $enumClass = $arguments[0];
        $enumValues = $arguments[1];

        return new class($enumClass, $enumValues) implements NeonConfigCasterContract
        {
            use HasEnumUtils;

            public function __construct(protected string $enumClass, protected array $enumValues) {}

            public function to(): mixed
            {
                return collect($this->enumValues)
                    ->map(function ($enumValue) {
                        return $this->getEnum($this->enumClass, $enumValue);
                    })
                    ->toArray();
            }

            public function from(): array
            {
                return collect($this->enumValues)
                    ->map(function ($enum) {
                        return $this->getStorableEnumValue($enum);
                    })
                    ->toArray();
            }
        };
    }

    public static function of(string $class): string
    {
        return static::class.':'.$class;
    }
}
