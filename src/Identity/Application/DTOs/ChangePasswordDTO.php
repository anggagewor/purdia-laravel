<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class ChangePasswordDTO
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {}
}
