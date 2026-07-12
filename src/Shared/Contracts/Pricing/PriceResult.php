<?php

namespace Purdia\Shared\Contracts\Pricing;

final readonly class PriceResult
{
    public function __construct(
        public float $basePrice,
        public float $finalPrice,
        public ?string $priceListId = null,
        public ?string $priceListName = null,
        public ?string $currency = null,
        public array $appliedPromotions = [],
    ) {}
}
