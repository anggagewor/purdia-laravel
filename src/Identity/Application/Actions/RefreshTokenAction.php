<?php

namespace Purdia\Identity\Application\Actions;

use Purdia\Identity\Application\DTOs\AuthTokenDTO;
use Purdia\Identity\Domain\Models\User;

class RefreshTokenAction
{
    public function execute(User $user): AuthTokenDTO
    {
        // Delete current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth')->plainTextToken;

        return new AuthTokenDTO(
            accessToken: $token,
            tokenType: 'Bearer',
        );
    }
}
