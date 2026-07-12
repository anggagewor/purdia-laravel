<?php

namespace Purdia\Shared\DTOs\Party;

final readonly class PartyDTO
{
    public function __construct(
        public string $id,
        public string $type,
        public string $displayName,
        public ?string $code = null,
        public array $roles = [],
    ) {}
}
