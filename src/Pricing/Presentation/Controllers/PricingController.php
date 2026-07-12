<?php

namespace Purdia\Pricing\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Pricing\Domain\Models\Discount;
use Purdia\Pricing\Domain\Models\PriceList;
use Purdia\Pricing\Domain\Models\Promotion;
use Purdia\Shared\Contracts\Pricing\PricingEngine;
use Purdia\Shared\Support\ApiResponse;
use Purdia\Tenant\Application\Context\TenantContext;

class PricingController extends Controller
{
    /**
     * Resolve price for a product.
     * POST /api/pricing/resolve
     */
    public function resolve(Request $request, PricingEngine $engine): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'price_list_id' => ['nullable', 'integer'],
        ]);

        $result = $engine->resolve(
            productId: (string) $request->get('product_id'),
            tenantId: (string) TenantContext::tenantId(),
            branchId: $request->get('branch_id') ? (string) $request->get('branch_id') : null,
            variantId: $request->get('variant_id') ? (string) $request->get('variant_id') : null,
            qty: $request->get('qty', 1),
            priceListId: $request->get('price_list_id') ? (string) $request->get('price_list_id') : null,
        );

        return ApiResponse::success($result);
    }

    /**
     * Apply a discount code.
     * POST /api/pricing/discount
     */
    public function applyDiscount(Request $request, PricingEngine $engine): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $engine->applyDiscount(
            discountCode: $request->get('code'),
            amount: (float) $request->get('amount'),
            tenantId: (string) TenantContext::tenantId(),
        );

        return ApiResponse::success($result);
    }

    // --- CRUD for Price Lists ---

    public function priceLists(): JsonResponse
    {
        $lists = PriceList::where('is_active', true)->orderBy('name')->get();

        return ApiResponse::success($lists);
    }

    public function storePriceList(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100'],
            'type' => ['sometimes', 'string', 'in:selling,buying'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $priceList = PriceList::create($request->only(['name', 'slug', 'type', 'currency', 'is_default']));

        return ApiResponse::created($priceList);
    }

    // --- CRUD for Discounts ---

    public function discounts(): JsonResponse
    {
        $discounts = Discount::orderBy('name')->get();

        return ApiResponse::success($discounts);
    }

    public function storeDiscount(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ]);

        $discount = Discount::create($request->only([
            'name', 'code', 'type', 'value', 'min_order_amount',
            'max_discount_amount', 'usage_limit', 'starts_at', 'ends_at',
        ]));

        return ApiResponse::created($discount);
    }

    // --- CRUD for Promotions ---

    public function promotions(): JsonResponse
    {
        $promotions = Promotion::with('rules')->orderBy('priority')->get();

        return ApiResponse::success($promotions);
    }

    public function storePromotion(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'scope' => ['sometimes', 'string', 'in:product,category,brand,cart,global'],
            'discount_type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_qty' => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', 'integer'],
            'is_combinable' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'rules' => ['nullable', 'array'],
            'rules.*.entity_type' => ['required', 'string', 'in:product,category,brand'],
            'rules.*.entity_id' => ['required', 'string'],
        ]);

        $promotion = Promotion::create($request->except('rules'));

        if ($request->has('rules')) {
            foreach ($request->get('rules') as $rule) {
                $promotion->rules()->create($rule);
            }
        }

        return ApiResponse::created($promotion->load('rules'));
    }
}
