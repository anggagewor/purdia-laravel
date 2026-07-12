<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\DTOs\CreatePermissionDTO;
use Purdia\Authorization\Application\Exceptions\PermissionNameAlreadyExistsException;
use Purdia\Authorization\Domain\Models\Permission;

class CreatePermissionAction
{
    public function execute(CreatePermissionDTO $dto): Permission
    {
        if (Permission::where('name', $dto->name)->exists()) {
            throw new PermissionNameAlreadyExistsException($dto->name);
        }

        return Permission::create([
            'name' => $dto->name,
            'scope' => $dto->scope,
            'description' => $dto->description,
        ]);
    }
}
