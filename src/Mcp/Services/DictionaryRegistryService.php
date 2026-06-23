<?php

namespace Uneca\Chimera\Mcp\Services;

use Illuminate\Support\Facades\File;

class DictionaryRegistryService
{
    private const CONFIG_PATH = 'dashboard-starter-kit-mcp.json';

    public function registeredDictionaries(): array
    {
        $configPath = base_path(self::CONFIG_PATH);

        if (! File::exists($configPath)) {
            return [];
        }

        $config = json_decode(File::get($configPath), true);

        if (! is_array($config)) {
            return [];
        }

        return $config['dictionaries'] ?? [];
    }

    public function isInitialized(): bool
    {
        return $this->registeredDictionaries() !== [];
    }

    public function registeredDataSources(): string
    {
        return implode(', ', array_keys($this->registeredDictionaries()));
    }

    public function abortMessage(): string
    {
        $registered = $this->registeredDataSources();

        return "The MCP server has not been initialized. Ask the user to run `php artisan chimera:mcp-init` in the consumer app to register dictionary files, then retry. Do NOT proceed without a dictionary — do not fall back to database schema introspection, file exploration, or any other workaround. Do NOT attempt to run `chimera:mcp-init` yourself — it prompts for dictionary file paths that only the user can provide. Registered data sources: {$registered}";
    }
}
