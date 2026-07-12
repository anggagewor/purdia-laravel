<?php

namespace Purdia\Pricing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'product_id',
        'variant_id',
        'branch_id',
        'min_qty',
        'price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'min_qty' => 'integer',
            'price' => 'decimal:4',
        ];
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }
}
