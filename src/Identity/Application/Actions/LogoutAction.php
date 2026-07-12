<?php

namespace Purdia\Identity\Application\Actions;

use Purdia\Identity\Domain\Models\User;

class LogoutAction
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
