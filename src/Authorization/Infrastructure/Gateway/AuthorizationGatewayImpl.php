<?php

namespace Purdia\Authorization\Infrastructure\Gateway;

use Purdia\Authorization\Domain\Contracts\PermissionRepository;
use Purdia\Authorization\Domain\Contracts\RoleRepository;
use Purdia\Shared\Contracts\Authorization\AuthorizationGateway;

class AuthorizationGatewayImpl implements AuthorizationGateway
{
    public function __construct(
        private readonly PermissionRepository $permissions,
        private readonly RoleRepository $roles,
    ) {}

    public function userCan(string $userId, string $permission): bool
    {
        return $this->permissions->userHasPermission($userId, $permission);
    }

    public function userPermissions(string $userId): array
    {
        return $this->permissions->userPermissions($userId)
            ->pluck('name')
            ->all();
    }

    public function userRoles(string $userId): array
    {
        return $this->roles->userRoles($userId)
            ->pluck('slug')
            ->all();
    }
}
