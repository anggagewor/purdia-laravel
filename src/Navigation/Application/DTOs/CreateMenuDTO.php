<?php

namespace Purdia\Navigation\Application\DTOs;

final readonly class CreateMenuDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $path = null,
        public ?string $icon = null,
        public ?string $permission = null,
        public ?string $parentId = null,
        public int $sortOrder = 0,
        public bool $isVisible = true,
    ) {}
}
