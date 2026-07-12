<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class ResetPasswordDTO
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}
}
