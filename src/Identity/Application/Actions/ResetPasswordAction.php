<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Purdia\Identity\Application\DTOs\ResetPasswordDTO;
use Purdia\Identity\Application\Exceptions\InvalidResetTokenException;

class ResetPasswordAction
{
    public function execute(ResetPasswordDTO $dto): void
    {
        $status = Password::reset(
            [
                'email' => $dto->email,
                'token' => $dto->token,
                'password' => $dto->password,
                'password_confirmation' => $dto->password,
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Revoke all tokens on password reset
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new InvalidResetTokenException();
        }
    }
}
