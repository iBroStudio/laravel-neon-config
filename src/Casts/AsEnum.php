<?php

namespace IBroStudio\NeonConfig\Casts;

use IBroStudio\NeonConfig\Concerns\HasEnumUtils;
use IBroStudio\NeonConfig\Contracts\NeonConfigCasterContract;

class AsEnum implements NeonConfigCasterContract
{
    use HasEnumUtils;

    public function __construct(protected mixed $enumValue, protected string $enumClass) {}

    public function to(): mixed
    {
        return $this->getEnum($this->enumClass, $this->enumValue);
    }

    public function from(): string|int
    {
        return $this->getStorableEnumValue($this->enumValue);
    }
}
