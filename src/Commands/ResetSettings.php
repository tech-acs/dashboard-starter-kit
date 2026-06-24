<?php

namespace Uneca\Chimera\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetSettings extends Command
{
    protected $signature = 'chimera:reset-settings {--force : Force the operation without confirmation}';

    protected $description = 'Truncate and re-initialize all settings to defaults (clears settings cache)';

    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will DELETE all custom settings and reset them to defaults. Are you sure?')) {
                return self::FAILURE;
            }
        }

        $this->components->task('Resetting settings to defaults', function () {
            initializeSettings();
            Cache::forget('settings');
        });

        $this->components->info('Settings have been reset to defaults and the settings cache has been cleared.');

        return self::SUCCESS;
    }
}
