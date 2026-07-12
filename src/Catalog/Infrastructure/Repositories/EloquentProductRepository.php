<?php

namespace Purdia\Catalog\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Catalog\Domain\Contracts\ProductRepository;
use Purdia\Catalog\Domain\Models\Product;

class EloquentProductRepository implements ProductRepository
{
    public function findById(string $id): ?Product
    {
        return Product::with(['category', 'brand', 'variants', 'attributes.definition'])->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return Product::where('barcode', $barcode)->first();
    }

    public function search(string $query, ?string $categoryId = null, ?string $brandId = null): Collection
    {
        return Product::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($brandId, fn ($q) => $q->where('brand_id', $brandId))
            ->where('is_active', true)
            ->with(['category', 'brand'])
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }
}
