<?php

namespace Purdia\Authorization\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Authorization\Domain\Models\Role;

interface RoleRepository
{
    public function findById(string $id): ?Role;

    public function findBySlug(string $slug): ?Role;

    public function userRoles(string $userId): Collection;

    public function all(): Collection;
}
