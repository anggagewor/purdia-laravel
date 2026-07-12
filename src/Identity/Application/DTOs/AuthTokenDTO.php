<?php

namespace Purdia\Identity\Application\DTOs;

final readonly class AuthTokenDTO
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public ?string $expiresAt = null,
    ) {}
}
