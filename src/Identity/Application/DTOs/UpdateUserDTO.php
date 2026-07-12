<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class UpdateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?string $roleId = null,
        public ?array $branchIds = null,
    ) {}
}
