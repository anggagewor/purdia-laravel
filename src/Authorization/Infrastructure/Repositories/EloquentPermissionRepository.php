<?php

namespace Purdia\Authorization\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Contracts\PermissionRepository;
use Purdia\Authorization\Domain\Models\Permission;
use Purdia\Identity\Domain\Models\User;

class EloquentPermissionRepository implements PermissionRepository
{
    public function findByName(string $name): ?Permission
    {
        return Permission::where('name', $name)->first();
    }

    public function userPermissions(string $userId): Collection
    {
        $user = User::find($userId);

        if (! $user) {
            return collect();
        }

        return $user->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('id')
            ->values();
    }

    public function userHasPermission(string $userId, string $permission): bool
    {
        $user = User::find($userId);

        if (! $user) {
            return false;
        }

        return $user->hasPermission($permission);
    }
}
