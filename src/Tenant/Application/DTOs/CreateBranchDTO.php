<?php

namespace Purdia\Tenant\Application\DTOs;

final readonly class CreateBranchDTO
{
    public function __construct(
        public string $tenantId,
        public string $name,
        public string $code,
        public string $type = 'store',
        public ?string $parentBranchId = null,
        public ?string $address = null,
        public ?string $phone = null,
        public ?string $timezone = null,
        public ?array $settings = null,
    ) {}
}
