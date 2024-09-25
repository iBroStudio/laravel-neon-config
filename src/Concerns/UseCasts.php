<?php

namespace IBroStudio\NeonConfig\Concerns;

use Exception;
use IBroStudio\NeonConfig\Casts\AsConfig;
use IBroStudio\NeonConfig\Casts\AsEnum;
use IBroStudio\NeonConfig\Casts\AsEnumCollection;
use IBroStudio\NeonConfig\Casts\AsEnv;
use IBroStudio\NeonConfig\Contracts\NeonConfigCasterContract;
use Illuminate\Support\Str;
use Nette\Neon\Entity;

trait UseCasts
{
    protected function cast(string $laravelConfigKey, mixed $value = null): NeonConfigCasterContract
    {
        if (enum_exists($this->casts[$laravelConfigKey])) {
            return new AsEnum(
                enumValue: $value,
                enumClass: $this->casts[$laravelConfigKey]
            );
        }

        if (Str::startsWith($this->casts[$laravelConfigKey], 'AsEnumCollection')) {
            return AsEnumCollection::castUsing([
                Str::after($this->casts[$laravelConfigKey], '::'),
                $value,
            ]);
        }

        if (Str::startsWith($this->casts[$laravelConfigKey], 'AsConfig')) {
            if ($value instanceof Entity) {
                return new AsConfig($value);
            }

            return new AsConfig(Str::after($this->casts[$laravelConfigKey], '::'));
        }

        if (Str::startsWith($this->casts[$laravelConfigKey], 'AsEnv')) {
            if ($value instanceof Entity) {
                return new AsEnv($value);
            }

            return new AsEnv(Str::after($this->casts[$laravelConfigKey], '::'));
        }

        //throw new Exception('Cast not found for config key : '.$laravelConfigKey);

        return new ('\\IBroStudio\\NeonConfig\\Casts\\'.$this->casts[$laravelConfigKey])($value);
    }

    protected function getCast(Entity $entity)
    {
        return match ($entity->value) {
            'AsEnumCollection' => AsEnumCollection::of($entity->attributes['of']),
            default => throw new Exception('Cast type not supported: '.$entity->value),
        };
    }
}
