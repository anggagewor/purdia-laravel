<?php

namespace Purdia\Shared\Contracts\Pricing;

interface PricingEngine
{
    /**
     * Resolve the best price for a product.
     *
     * @param string $productId
     * @param string $tenantId
     * @param string|null $branchId
     * @param string|null $variantId
     * @param int $qty
     * @param string|null $priceListId Specific price list (null = default)
     * @return PriceResult
     */
    public function resolve(
        string $productId,
        string $tenantId,
        ?string $branchId = null,
        ?string $variantId = null,
        int $qty = 1,
        ?string $priceListId = null,
    ): PriceResult;

    /**
     * Apply a discount code to an amount.
     */
    public function applyDiscount(string $discountCode, float $amount, string $tenantId): DiscountResult;

    /**
     * Get applicable promotions for a product/cart.
     */
    public function getApplicablePromotions(
        string $tenantId,
        ?string $productId = null,
        ?string $categoryId = null,
        ?string $brandId = null,
        float $orderAmount = 0,
        int $qty = 1,
    ): array;
}
