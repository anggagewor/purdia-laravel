<?php

namespace Purdia\Storage\Application\DTOs;

use Purdia\Storage\Domain\Enums\FileVisibility;

final readonly class UploadFileDTO
{
    public function __construct(
        public string $module,
        public ?string $entityType = null,
        public ?string $entityId = null,
        public ?string $disk = null,
        public ?string $pathPrefix = null,
        public FileVisibility $visibility = FileVisibility::Private,
        public ?array $metadata = null,
    ) {}
}
