<?php

namespace IBroStudio\NeonConfig\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IBroStudio\NeonConfig\NeonConfig
 */
class NeonConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \IBroStudio\NeonConfig\NeonConfig::class;
    }
}
