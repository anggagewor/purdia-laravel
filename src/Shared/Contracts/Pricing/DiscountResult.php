<?php

namespace Purdia\Shared\Contracts\Pricing;

final readonly class DiscountResult
{
    public function __construct(
        public bool $applied,
        public float $originalAmount,
        public float $discountAmount,
        public float $finalAmount,
        public ?string $discountCode = null,
        public ?string $error = null,
    ) {}
}
