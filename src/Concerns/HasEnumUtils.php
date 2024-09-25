<?php

namespace IBroStudio\NeonConfig\Concerns;

use BackedEnum;

trait HasEnumUtils
{
    public function getEnum(string $enumClass, $enumValue): mixed
    {
        return is_subclass_of($enumClass, BackedEnum::class)
            ? $this->enumClass::from($enumValue)
            : constant($enumClass.'::'.$enumValue);
    }

    protected function getStorableEnumValue(mixed $enum)
    {
        if (is_string($enum) || is_int($enum)) {
            return $enum;
        }

        return $enum instanceof BackedEnum ? $enum->value : $enum->name;
    }
}
