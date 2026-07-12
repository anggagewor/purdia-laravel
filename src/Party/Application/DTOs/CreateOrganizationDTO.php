<?php

namespace Purdia\Party\Application\DTOs;

final readonly class CreateOrganizationDTO
{
    public function __construct(
        public string $legalName,
        public ?string $displayName = null,
        public ?string $code = null,
        public ?string $taxNumber = null,
        public ?string $npwp = null,
        public ?string $nib = null,
        public ?string $industry = null,
        public ?string $website = null,
        public ?array $contacts = null,
        public ?array $addresses = null,
        public ?array $roles = null,
    ) {}
}
