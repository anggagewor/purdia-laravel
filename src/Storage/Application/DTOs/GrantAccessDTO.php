<?php

namespace Purdia\Storage\Application\DTOs;

use Purdia\Storage\Domain\Enums\FileAccessLevel;

final readonly class GrantAccessDTO
{
    public function __construct(
        public string $fileId,
        public string $accessorType,
        public string $accessorId,
        public FileAccessLevel $accessLevel = FileAccessLevel::ReadOnly,
    ) {}
}
