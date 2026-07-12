<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
