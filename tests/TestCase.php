<?php

namespace Uneca\Chimera\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Mcp\Server\McpServiceProvider;
use Opcodes\LogViewer\LogViewerServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Uneca\Chimera\ChimeraServiceProvider;

class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Uneca\\CensusDashboardStarterKit\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ChimeraServiceProvider::class,
            LogViewerServiceProvider::class,
            McpServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('session.driver', 'array');
    }
}
