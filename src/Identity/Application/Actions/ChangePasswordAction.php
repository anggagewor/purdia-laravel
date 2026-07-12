<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\Hash;
use Purdia\Identity\Application\DTOs\ChangePasswordDTO;
use Purdia\Identity\Application\Exceptions\CurrentPasswordMismatchException;
use Purdia\Identity\Domain\Models\User;

class ChangePasswordAction
{
    public function execute(User $user, ChangePasswordDTO $dto): void
    {
        if (! Hash::check($dto->currentPassword, $user->password)) {
            throw new CurrentPasswordMismatchException();
        }

        $user->update([
            'password' => $dto->newPassword,
        ]);
    }
}
