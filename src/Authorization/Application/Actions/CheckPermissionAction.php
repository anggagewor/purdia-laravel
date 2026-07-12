<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Domain\Contracts\PermissionRepository;

class CheckPermissionAction
{
    public function __construct(
        private readonly PermissionRepository $permissions,
    ) {}

    public function execute(string $userId, string $permission): bool
    {
        return $this->permissions->userHasPermission($userId, $permission);
    }
}
