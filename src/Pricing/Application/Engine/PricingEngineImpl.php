<?php

namespace Purdia\Pricing\Application\Engine;

use Purdia\Pricing\Domain\Enums\DiscountType;
use Purdia\Pricing\Domain\Models\Discount;
use Purdia\Pricing\Domain\Models\PriceList;
use Purdia\Pricing\Domain\Models\PriceListItem;
use Purdia\Pricing\Domain\Models\Promotion;
use Purdia\Shared\Contracts\Pricing\DiscountResult;
use Purdia\Shared\Contracts\Pricing\PriceResult;
use Purdia\Shared\Contracts\Pricing\PricingEngine;

class PricingEngineImpl implements PricingEngine
{
    public function resolve(
        string $productId,
        string $tenantId,
        ?string $branchId = null,
        ?string $variantId = null,
        int $qty = 1,
        ?string $priceListId = null,
    ): PriceResult {
        // Find applicable price list
        $priceList = $this->resolvePriceList($tenantId, $priceListId);

        if (! $priceList) {
            return new PriceResult(basePrice: 0, finalPrice: 0);
        }

        // Find best price from price list items
        $basePrice = $this->findBestPrice($priceList, $productId, $variantId, $branchId, $qty);

        // Apply auto-promotions
        $promotionDiscount = $this->calculatePromotionDiscount($tenantId, $productId, $basePrice, $qty);

        $finalPrice = max(0, $basePrice - $promotionDiscount['total']);

        return new PriceResult(
            basePrice: $basePrice,
            finalPrice: $finalPrice,
            priceListId: (string) $priceList->id,
            priceListName: $priceList->name,
            currency: $priceList->currency,
            appliedPromotions: $promotionDiscount['applied'],
        );
    }

    public function applyDiscount(string $discountCode, float $amount, string $tenantId): DiscountResult
    {
        $discount = Discount::where('tenant_id', $tenantId)
            ->where('code', $discountCode)
            ->first();

        if (! $discount) {
            return new DiscountResult(
                applied: false,
                originalAmount: $amount,
                discountAmount: 0,
                finalAmount: $amount,
                error: 'Discount code not found.',
            );
        }

        if (! $discount->isValid()) {
            return new DiscountResult(
                applied: false,
                originalAmount: $amount,
                discountAmount: 0,
                finalAmount: $amount,
                discountCode: $discountCode,
                error: 'Discount code is expired or inactive.',
            );
        }

        if ($discount->min_order_amount && $amount < (float) $discount->min_order_amount) {
            return new DiscountResult(
                applied: false,
                originalAmount: $amount,
                discountAmount: 0,
                finalAmount: $amount,
                discountCode: $discountCode,
                error: "Minimum order amount is {$discount->min_order_amount}.",
            );
        }

        $discountAmount = $this->calculateDiscount($discount->type, (float) $discount->value, $amount);

        // Cap discount
        if ($discount->max_discount_amount && $discountAmount > (float) $discount->max_discount_amount) {
            $discountAmount = (float) $discount->max_discount_amount;
        }

        return new DiscountResult(
            applied: true,
            originalAmount: $amount,
            discountAmount: $discountAmount,
            finalAmount: max(0, $amount - $discountAmount),
            discountCode: $discountCode,
        );
    }

    public function getApplicablePromotions(
        string $tenantId,
        ?string $productId = null,
        ?string $categoryId = null,
        ?string $brandId = null,
        float $orderAmount = 0,
        int $qty = 1,
    ): array {
        $query = Promotion::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('priority');

        if ($orderAmount > 0) {
            $query->where(function ($q) use ($orderAmount) {
                $q->whereNull('min_order_amount')->orWhere('min_order_amount', '<=', $orderAmount);
            });
        }

        if ($qty > 0) {
            $query->where(function ($q) use ($qty) {
                $q->whereNull('min_qty')->orWhere('min_qty', '<=', $qty);
            });
        }

        $promotions = $query->with('rules')->get();

        // Filter by scope rules
        return $promotions->filter(function ($promo) use ($productId, $categoryId, $brandId) {
            if ($promo->rules->isEmpty()) {
                return true; // Global promotion, no specific rules
            }

            foreach ($promo->rules as $rule) {
                if ($rule->entity_type === 'product' && $rule->entity_id == $productId) {
                    return true;
                }
                if ($rule->entity_type === 'category' && $rule->entity_id == $categoryId) {
                    return true;
                }
                if ($rule->entity_type === 'brand' && $rule->entity_id == $brandId) {
                    return true;
                }
            }

            return false;
        })->values()->toArray();
    }

    private function resolvePriceList(string $tenantId, ?string $priceListId): ?PriceList
    {
        if ($priceListId) {
            return PriceList::where('tenant_id', $tenantId)
                ->where('id', $priceListId)
                ->where('is_active', true)
                ->first();
        }

        return PriceList::where('tenant_id', $tenantId)
            ->where('type', 'selling')
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    private function findBestPrice(PriceList $priceList, string $productId, ?string $variantId, ?string $branchId, int $qty): float
    {
        $query = PriceListItem::where('price_list_id', $priceList->id)
            ->where('product_id', $productId)
            ->where(function ($q) use ($qty) {
                $q->whereNull('min_qty')->orWhere('min_qty', '<=', $qty);
            })
            ->orderBy('min_qty', 'desc'); // Best qty match first

        if ($variantId) {
            $query->where(function ($q) use ($variantId) {
                $q->where('variant_id', $variantId)->orWhereNull('variant_id');
            });
        }

        if ($branchId) {
            // Prefer branch-specific price, fallback to general
            $branchPrice = (clone $query)->where('branch_id', $branchId)->first();
            if ($branchPrice) {
                return (float) $branchPrice->price;
            }
        }

        $item = $query->whereNull('branch_id')->first();

        return $item ? (float) $item->price : 0;
    }

    private function calculatePromotionDiscount(string $tenantId, string $productId, float $basePrice, int $qty): array
    {
        $promotions = $this->getApplicablePromotions($tenantId, $productId, qty: $qty);
        $totalDiscount = 0;
        $applied = [];

        foreach ($promotions as $promo) {
            $promoModel = is_array($promo) ? (object) $promo : $promo;
            $discount = $this->calculateDiscount(
                DiscountType::from($promoModel->discount_type),
                (float) $promoModel->discount_value,
                $basePrice,
            );

            if ($promoModel->max_discount_amount && $discount > (float) $promoModel->max_discount_amount) {
                $discount = (float) $promoModel->max_discount_amount;
            }

            $totalDiscount += $discount;
            $applied[] = [
                'id' => $promoModel->id,
                'name' => $promoModel->name,
                'discount' => $discount,
            ];

            if (! ($promoModel->is_combinable ?? false)) {
                break; // Non-combinable = stop after first
            }
        }

        return ['total' => $totalDiscount, 'applied' => $applied];
    }

    private function calculateDiscount(DiscountType $type, float $value, float $amount): float
    {
        return match ($type) {
            DiscountType::Percentage => $amount * ($value / 100),
            DiscountType::Fixed => min($value, $amount),
        };
    }
}
