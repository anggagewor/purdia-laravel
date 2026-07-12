<?php

namespace Purdia\Authorization\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Contracts\PermissionRepository;
use Purdia\Authorization\Domain\Models\Permission;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Identity\Domain\Models\User;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\TenantUser;

class EloquentPermissionRepository implements PermissionRepository
{
    public function findByName(string $name): ?Permission
    {
        return Permission::where('name', $name)->first();
    }

    public function userPermissions(string $userId): Collection
    {
        $roleIds = $this->getUserRoleIds($userId);

        if ($roleIds->isEmpty()) {
            return collect();
        }

        return Role::whereIn('id', $roleIds)
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('id')
            ->values();
    }

    public function userHasPermission(string $userId, string $permission): bool
    {
        $roleIds = $this->getUserRoleIds($userId);

        if ($roleIds->isEmpty()) {
            return false;
        }

        return Permission::where('name', $permission)
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->exists();
    }

    /**
     * Get role IDs for a user.
     * If tenant context is resolved, get from tenant_users.
     * Otherwise fallback to user_role table.
     */
    private function getUserRoleIds(string $userId): Collection
    {
        // Prefer tenant-aware roles
        if (TenantContext::isResolved()) {
            return TenantUser::where('user_id', $userId)
                ->where('tenant_id', TenantContext::tenantId())
                ->where('is_active', true)
                ->pluck('role_id');
        }

        // Fallback: check tenant_users without tenant filter (for non-tenant routes)
        $tenantRoles = TenantUser::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('role_id');

        if ($tenantRoles->isNotEmpty()) {
            return $tenantRoles;
        }

        // Last fallback: legacy user_role table
        $user = User::find($userId);

        return $user ? $user->roles()->pluck('roles.id') : collect();
    }
}
