<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\Exceptions\RoleNotFoundException;
use Purdia\Authorization\Domain\Models\Role;

class DeleteRoleAction
{
    public function execute(string $roleId): void
    {
        $role = Role::find($roleId);

        if (! $role) {
            throw new RoleNotFoundException($roleId);
        }

        $role->delete();
    }
}
