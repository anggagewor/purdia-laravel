<?php

namespace Purdia\Pricing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRule extends Model
{
    protected $fillable = [
        'promotion_id',
        'entity_type',
        'entity_id',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }
}
