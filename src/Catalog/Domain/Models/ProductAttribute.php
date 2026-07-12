<?php

namespace Purdia\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Purdia\Catalog\Domain\Enums\AttributeType;

class ProductAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(AttributeDefinition::class, 'attribute_id');
    }
}
