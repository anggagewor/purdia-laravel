<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\Hash;
use Purdia\Identity\Application\DTOs\AuthTokenDTO;
use Purdia\Identity\Application\DTOs\LoginDTO;
use Purdia\Identity\Application\Exceptions\InvalidCredentialsException;
use Purdia\Identity\Domain\Contracts\UserRepository;

class LoginAction
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function execute(LoginDTO $dto): AuthTokenDTO
    {
        $user = $this->users->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $user->createToken('auth')->plainTextToken;

        return new AuthTokenDTO(
            accessToken: $token,
            tokenType: 'Bearer',
        );
    }
}
