<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class ForgotPasswordDTO
{
    public function __construct(
        public string $email,
    ) {}
}
