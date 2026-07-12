<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\Password;
use Purdia\Identity\Application\DTOs\ForgotPasswordDTO;

class ForgotPasswordAction
{
    public function execute(ForgotPasswordDTO $dto): string
    {
        $status = Password::sendResetLink(['email' => $dto->email]);

        return $status;
    }
}
