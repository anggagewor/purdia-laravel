<?php

namespace Purdia\Storage\Application\Actions;

use Purdia\Storage\Application\Exceptions\FileNotFoundException;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Enums\FileAccessLevel;
use Purdia\Storage\Domain\Enums\FileVisibility;
use Purdia\Storage\Domain\Models\File;
use Purdia\Storage\Domain\Models\FileAccess;

class CheckFileAccessAction
{
    public function __construct(
        private readonly FileRepository $files,
    ) {}

    /**
     * Check if a user/group has access to a file.
     * Returns the access level if granted, null if denied.
     */
    public function execute(string $fileId, string $userId, array $roleIds = []): ?FileAccessLevel
    {
        $file = $this->files->findById($fileId);

        if (! $file) {
            throw new FileNotFoundException($fileId);
        }

        // Public files — everyone can read
        if ($file->visibility === FileVisibility::Public) {
            return FileAccessLevel::ReadOnly;
        }

        // Owner always has full control
        if ($file->uploaded_by === $userId) {
            return FileAccessLevel::FullControl;
        }

        // Private files — only owner
        if ($file->visibility === FileVisibility::Private) {
            return null;
        }

        // Restricted files — check access list
        return $this->resolveAccessLevel($file, $userId, $roleIds);
    }

    private function resolveAccessLevel(File $file, string $userId, array $roleIds): ?FileAccessLevel
    {
        // Check user-level access
        $userAccess = FileAccess::where('file_id', $file->id)
            ->where('accessor_type', 'user')
            ->where('accessor_id', $userId)
            ->first();

        if ($userAccess) {
            return $userAccess->access_level;
        }

        // Check role-level access
        if (! empty($roleIds)) {
            $roleAccess = FileAccess::where('file_id', $file->id)
                ->where('accessor_type', 'role')
                ->whereIn('accessor_id', $roleIds)
                ->orderByRaw("FIELD(access_level, 'full_control', 'read_write', 'read_only')")
                ->first();

            if ($roleAccess) {
                return $roleAccess->access_level;
            }
        }

        return null;
    }
}
