<?php

namespace Purdia\Storage\Infrastructure\Repositories;

use Purdia\Storage\Domain\Contracts\StorageRuleResolver;
use Purdia\Storage\Domain\Models\StorageRule;

class EloquentStorageRuleResolver implements StorageRuleResolver
{
    public function resolve(string $mimeType, string $extension): ?StorageRule
    {
        // Try mime pattern first (more specific)
        $rule = StorageRule::where('is_active', true)
            ->where(function ($query) use ($mimeType, $extension) {
                $query->whereRaw("? LIKE REPLACE(mime_pattern, '*', '%')", [$mimeType])
                    ->orWhereRaw("? LIKE REPLACE(extension_pattern, '*', '%')", [$extension]);
            })
            ->orderByRaw("CASE WHEN mime_pattern IS NOT NULL AND mime_pattern != '*' THEN 0 ELSE 1 END")
            ->first();

        return $rule;
    }
}
