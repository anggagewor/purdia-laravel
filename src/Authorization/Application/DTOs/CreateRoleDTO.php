<?php

namespace Purdia\Authorization\Application\DTOs;

final readonly class CreateRoleDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
    ) {}
}
