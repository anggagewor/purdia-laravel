<?php

namespace Purdia\Identity\Infrastructure\Gateway;

use Purdia\Identity\Domain\Contracts\UserRepository;
use Purdia\Shared\Contracts\Identity\IdentityGateway;
use Purdia\Shared\DTOs\Identity\UserDTO;

class IdentityGatewayImpl implements IdentityGateway
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function resolveUser(string $id): ?UserDTO
    {
        $user = $this->users->findById($id);

        if (! $user) {
            return null;
        }

        return new UserDTO(
            id: (string) $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at?->toIsoString(),
        );
    }

    public function resolveUserByEmail(string $email): ?UserDTO
    {
        $user = $this->users->findByEmail($email);

        if (! $user) {
            return null;
        }

        return new UserDTO(
            id: (string) $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at?->toIsoString(),
        );
    }
}
