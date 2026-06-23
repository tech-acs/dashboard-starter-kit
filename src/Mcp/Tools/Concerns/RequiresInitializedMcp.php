<?php

namespace Uneca\Chimera\Mcp\Tools\Concerns;

use Laravel\Mcp\Response;
use Uneca\Chimera\Mcp\Services\DictionaryRegistryService;

trait RequiresInitializedMcp
{
    protected function abortIfNotInitialized(): ?Response
    {
        $registry = app(DictionaryRegistryService::class);

        if (! $registry->isInitialized()) {
            return Response::error($registry->abortMessage());
        }

        return null;
    }
}
