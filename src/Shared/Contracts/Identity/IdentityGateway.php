<?php

namespace Purdia\Shared\Contracts\Identity;

use Purdia\Shared\DTOs\Identity\UserDTO;

interface IdentityGateway
{
    public function resolveUser(string $id): ?UserDTO;

    public function resolveUserByEmail(string $email): ?UserDTO;
}
