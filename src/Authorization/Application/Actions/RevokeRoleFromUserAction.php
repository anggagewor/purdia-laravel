<?php

namespace Purdia\Authorization\Application\Actions;

use Purdia\Identity\Domain\Contracts\UserRepository;

class RevokeRoleFromUserAction
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function execute(string $userId, string $roleId): void
    {
        $user = $this->users->findById($userId);

        $user->roles()->detach($roleId);
    }
}
