<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\DTOs\UpdatePermissionDTO;
use Purdia\Authorization\Application\Exceptions\PermissionNameAlreadyExistsException;
use Purdia\Authorization\Application\Exceptions\PermissionNotFoundException;
use Purdia\Authorization\Domain\Models\Permission;

class UpdatePermissionAction
{
    public function execute(string $permissionId, UpdatePermissionDTO $dto): Permission
    {
        $permission = Permission::find($permissionId);

        if (! $permission) {
            throw new PermissionNotFoundException($permissionId);
        }

        $existingName = Permission::where('name', $dto->name)
            ->where('id', '!=', $permission->id)
            ->exists();

        if ($existingName) {
            throw new PermissionNameAlreadyExistsException($dto->name);
        }

        $permission->update([
            'name' => $dto->name,
            'scope' => $dto->scope,
            'description' => $dto->description,
        ]);

        return $permission->fresh();
    }
}
