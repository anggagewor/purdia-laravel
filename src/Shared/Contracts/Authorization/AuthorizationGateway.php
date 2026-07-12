<?php

namespace Purdia\Shared\Contracts\Authorization;

interface AuthorizationGateway
{
    /**
     * Check if a user has a specific permission.
     */
    public function userCan(string $userId, string $permission): bool;

    /**
     * Get all effective permissions for a user (union of all role permissions).
     */
    public function userPermissions(string $userId): array;

    /**
     * Get all roles assigned to a user.
     */
    public function userRoles(string $userId): array;
}
