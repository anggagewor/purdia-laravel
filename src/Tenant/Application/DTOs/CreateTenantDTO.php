<?php

namespace Purdia\Tenant\Application\DTOs;

final readonly class CreateTenantDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $currency = 'IDR',
        public string $locale = 'id',
        public string $timezone = 'Asia/Jakarta',
        public ?array $settings = null,
    ) {}
}
