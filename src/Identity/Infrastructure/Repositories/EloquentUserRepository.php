<?php

namespace Purdia\Identity\Infrastructure\Repositories;

use Purdia\Identity\Application\DTOs\RegisterDTO;
use Purdia\Identity\Domain\Contracts\UserRepository;
use Purdia\Identity\Domain\Models\User;

class EloquentUserRepository implements UserRepository
{
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(RegisterDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
