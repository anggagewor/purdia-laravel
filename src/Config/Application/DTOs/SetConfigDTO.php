<?php

namespace Purdia\Config\Application\DTOs;

final readonly class SetConfigDTO
{
    public function __construct(
        public string $group,
        public string $key,
        public mixed $value,
        public string $type = 'string',
    ) {}
}
