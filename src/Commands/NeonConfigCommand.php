<?php

namespace IBroStudio\NeonConfig\Commands;

use Illuminate\Console\Command;

class NeonConfigCommand extends Command
{
    public $signature = 'laravel-neon-config';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
