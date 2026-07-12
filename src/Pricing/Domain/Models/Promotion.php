<?php

namespace Purdia\Pricing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Purdia\Pricing\Domain\Enums\DiscountType;
use Purdia\Pricing\Domain\Enums\PromotionScope;
use Purdia\Shared\Traits\BelongsToTenant;

class Promotion extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'scope',
        'discount_type',
        'discount_value',
        'min_qty',
        'min_order_amount',
        'max_discount_amount',
        'priority',
        'is_combinable',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'scope' => PromotionScope::class,
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:4',
            'min_qty' => 'integer',
            'min_order_amount' => 'decimal:4',
            'max_discount_amount' => 'decimal:4',
            'priority' => 'integer',
            'is_combinable' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PromotionRule::class);
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }
}
