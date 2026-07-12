<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\Exceptions\RoleNotFoundException;
use Purdia\Authorization\Domain\Models\Role;

class SyncPermissionsToRoleAction
{
    public function execute(string $roleId, array $permissionIds): Role
    {
        $role = Role::find($roleId);

        if (! $role) {
            throw new RoleNotFoundException($roleId);
        }

        $role->permissions()->sync($permissionIds);

        return $role->load('permissions');
    }
}
