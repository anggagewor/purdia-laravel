<?php

namespace Purdia\Authorization\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Contracts\RoleRepository;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Identity\Domain\Models\User;

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
        $user = User::find($userId);

        if (! $user) {
            return collect();
        }

        return $user->roles;
    }

    public function all(): Collection
    {
        return Role::all();
    }
}
