<?php

namespace Purdia\Catalog\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Catalog\Domain\Models\Product;

interface ProductRepository
{
    public function findById(string $id): ?Product;

    public function findBySku(string $sku): ?Product;

    public function findByBarcode(string $barcode): ?Product;

    public function search(string $query, ?string $categoryId = null, ?string $brandId = null): Collection;

    public function create(array $data): Product;
}
