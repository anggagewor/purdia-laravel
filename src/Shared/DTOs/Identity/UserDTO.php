<?php

namespace Purdia\Shared\DTOs\Identity;

final readonly class UserDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $emailVerifiedAt = null,
    ) {}
}
