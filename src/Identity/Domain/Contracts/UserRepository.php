<?php

namespace Purdia\Identity\Domain\Contracts;

use Purdia\Identity\Application\DTOs\RegisterDTO;
use Purdia\Identity\Domain\Models\User;

interface UserRepository
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(RegisterDTO $dto): User;

    public function existsByEmail(string $email): bool;
}
