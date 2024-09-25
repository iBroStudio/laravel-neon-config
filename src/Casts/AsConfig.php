<?php

namespace IBroStudio\NeonConfig\Casts;

use IBroStudio\NeonConfig\Contracts\NeonConfigCasterContract;
use Nette\Neon\Entity;

class AsConfig implements NeonConfigCasterContract
{
    public function __construct(protected Entity|string $key, protected mixed $default = null)
    {
        if ($this->key instanceof Entity) {
            $this->default = $this->key->attributes['default'] ?? null;
            $this->key = $this->key->value;
        }
    }

    public function to(): mixed
    {
        return config($this->key, $this->default);
    }

    public function from(): Entity
    {
        return new Entity($this->key, ['type' => 'config', 'default' => $this->default]);
    }
}
