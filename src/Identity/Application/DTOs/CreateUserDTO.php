<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $roleId = null,
        public ?array $branchIds = null,
    ) {}
}
