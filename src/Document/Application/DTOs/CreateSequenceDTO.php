<?php

namespace Purdia\Document\Application\DTOs;

final readonly class CreateSequenceDTO
{
    public function __construct(
        public string $tenantId,
        public string $type,
        public string $prefix,
        public string $format = '{PREFIX}-{BRANCH}-{YYYY}{MM}-{####}',
        public string $resetFrequency = 'monthly',
        public ?string $branchId = null,
    ) {}
}
