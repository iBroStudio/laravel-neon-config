<?php

namespace IBroStudio\NeonConfig\Contracts;

interface NeonConfigCasterContract
{
    public function to(): mixed;

    public function from(): mixed;
}
