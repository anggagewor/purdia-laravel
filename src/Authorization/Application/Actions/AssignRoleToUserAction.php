<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Authorization\Application\Exceptions\RoleNotFoundException;
use Purdia\Authorization\Domain\Contracts\RoleRepository;
use Purdia\Identity\Domain\Contracts\UserRepository;

class AssignRoleToUserAction
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
    ) {}

    public function execute(string $userId, string $roleId): void
    {
        $user = $this->users->findById($userId);
        $role = $this->roles->findById($roleId);

        if (! $role) {
            throw new RoleNotFoundException($roleId);
        }

        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
