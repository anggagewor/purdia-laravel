<?php

namespace Purdia\Authorization\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Models\Permission;

interface PermissionRepository
{
    public function findByName(string $name): ?Permission;

    public function userPermissions(string $userId): Collection;

    public function userHasPermission(string $userId, string $permission): bool;
}
