<?php

namespace Purdia\Storage\Application\Actions;

use Purdia\Storage\Domain\Models\FileAccess;

class RevokeFileAccessAction
{
    public function execute(string $fileId, string $accessorType, string $accessorId): void
    {
        FileAccess::where('file_id', $fileId)
            ->where('accessor_type', $accessorType)
            ->where('accessor_id', $accessorId)
            ->delete();
    }
}
