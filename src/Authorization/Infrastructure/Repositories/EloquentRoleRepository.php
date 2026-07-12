<?php

namespace Purdia\Authorization\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Contracts\RoleRepository;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Identity\Domain\Models\User;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\TenantUser;

class EloquentRoleRepository implements RoleRepository
{
    public function findById(string $id): ?Role
    {
        return Role::find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    public function userRoles(string $userId): Collection
    {
        // Prefer tenant-aware roles
        if (TenantContext::isResolved()) {
            $roleIds = TenantUser::where('user_id', $userId)
                ->where('tenant_id', TenantContext::tenantId())
                ->where('is_active', true)
                ->pluck('role_id');

            return Role::whereIn('id', $roleIds)->get();
        }

        // Fallback
        $roleIds = TenantUser::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('role_id');

        if ($roleIds->isNotEmpty()) {
            return Role::whereIn('id', $roleIds)->get();
        }

        $user = User::find($userId);

        return $user ? $user->roles : collect();
    }

    public function all(): Collection
    {
        return Role::all();
    }
}
