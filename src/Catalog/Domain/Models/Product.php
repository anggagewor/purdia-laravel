<?php

namespace Purdia\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Purdia\Catalog\Domain\Enums\ProductType;
use Purdia\Shared\Traits\BelongsToTenant;
use Purdia\Shared\Traits\HasAudit;

class Product extends Model
{
    use BelongsToTenant, HasAudit, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'type',
        'description',
        'unit',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
