<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\DTOs\UpdateRoleDTO;
use Purdia\Authorization\Application\Exceptions\RoleNotFoundException;
use Purdia\Authorization\Application\Exceptions\RoleSlugAlreadyExistsException;
use Purdia\Authorization\Domain\Models\Role;

class UpdateRoleAction
{
    public function execute(string $roleId, UpdateRoleDTO $dto): Role
    {
        $role = Role::find($roleId);

        if (! $role) {
            throw new RoleNotFoundException($roleId);
        }

        $existingSlug = Role::where('slug', $dto->slug)
            ->where('id', '!=', $role->id)
            ->exists();

        if ($existingSlug) {
            throw new RoleSlugAlreadyExistsException($dto->slug);
        }

        $role->update([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
        ]);

        return $role->fresh();
    }
}
