<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\Exceptions\PermissionNotFoundException;
use Purdia\Authorization\Domain\Models\Permission;

class DeletePermissionAction
{
    public function execute(string $permissionId): void
    {
        $permission = Permission::find($permissionId);

        if (! $permission) {
            throw new PermissionNotFoundException($permissionId);
        }

        $permission->delete();
    }
}
