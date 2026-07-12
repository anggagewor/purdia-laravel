<?php

namespace Purdia\Storage\Domain\Contracts;

use Purdia\Storage\Domain\Models\StorageRule;

interface StorageRuleResolver
{
    /**
     * Resolve which storage rule applies for a given file.
     * Matches by mime type and/or extension.
     */
    public function resolve(string $mimeType, string $extension): ?StorageRule;
}
