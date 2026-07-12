<?php

namespace Purdia\Authorization\Application\DTOs;

final readonly class CreatePermissionDTO
{
    public function __construct(
        public string $name,
        public string $scope,
        public ?string $description = null,
    ) {}
}
