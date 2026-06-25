<?php

namespace Uneca\Chimera\DTOs;

readonly class GetDataResult
{
    public function __construct(
        public bool $success = false,
        public int $rowsReturned = 0,
        public array $columns = [],
        public ?object $sampleData = null,
        public ?string $error = null,
    ) {}
}
