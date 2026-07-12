<?php

namespace Purdia\Catalog\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Catalog\Domain\Contracts\ProductRepository;
use Purdia\Catalog\Domain\Models\Product;
use Purdia\Catalog\Presentation\Resources\V1\ProductResource;
use Purdia\Shared\Support\ApiResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->when($request->get('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->get('brand_id'), fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->get('type'), fn ($q, $v) => $q->where('type', $v))
            ->when($request->get('search'), fn ($q, $v) => $q->where(function ($sq) use ($v) {
                $sq->where('name', 'like', "%{$v}%")
                    ->orWhere('sku', 'like', "%{$v}%")
                    ->orWhere('barcode', 'like', "%{$v}%");
            }))
            ->orderBy('name');

        $products = $query->paginate($request->get('per_page', 50));

        return ApiResponse::success(ProductResource::collection($products));
    }

    public function show(string $product): JsonResponse
    {
        $product = $this->products->findById($product);

        if (! $product) {
            return response()->json(['error' => ['code' => 'CATALOG.PRODUCT_NOT_FOUND', 'message' => 'Product not found.']], 404);
        }

        return ApiResponse::success(new ProductResource($product));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'type' => ['sometimes', 'string', 'in:goods,service,digital,bundle'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
        ]);

        $product = $this->products->create($request->only([
            'name', 'slug', 'sku', 'barcode', 'type', 'category_id', 'brand_id', 'description', 'unit',
        ]));

        return ApiResponse::created(new ProductResource($product->load(['category', 'brand'])));
    }

    public function destroy(string $product): JsonResponse
    {
        $product = Product::findOrFail($product);
        $product->delete();

        return ApiResponse::success(message: 'Product deleted successfully.');
    }
}
