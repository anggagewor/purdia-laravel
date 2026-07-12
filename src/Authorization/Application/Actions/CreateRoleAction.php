<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\DTOs\CreateRoleDTO;
use Purdia\Authorization\Application\Exceptions\RoleSlugAlreadyExistsException;
use Purdia\Authorization\Domain\Models\Role;

class CreateRoleAction
{
    public function execute(CreateRoleDTO $dto): Role
    {
        if (Role::where('slug', $dto->slug)->exists()) {
            throw new RoleSlugAlreadyExistsException($dto->slug);
        }

        return Role::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
        ]);
    }
}
