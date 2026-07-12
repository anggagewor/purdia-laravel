<?php

namespace Purdia\Identity\Application\Actions;

use Purdia\Identity\Application\DTOs\AuthTokenDTO;
use Purdia\Identity\Application\DTOs\RegisterDTO;
use Purdia\Identity\Application\Exceptions\UserAlreadyExistsException;
use Purdia\Identity\Domain\Contracts\UserRepository;
use Purdia\Shared\Events\Identity\UserRegistered;

class RegisterAction
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function execute(RegisterDTO $dto): AuthTokenDTO
    {
        if ($this->users->existsByEmail($dto->email)) {
            throw new UserAlreadyExistsException($dto->email);
        }

        $user = $this->users->create($dto);

        UserRegistered::dispatch($user->id, $user->email, $user->name);

        $token = $user->createToken('auth')->plainTextToken;

        return new AuthTokenDTO(
            accessToken: $token,
            tokenType: 'Bearer',
        );
    }
}
