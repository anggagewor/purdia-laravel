<?php

namespace Purdia\Pricing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Purdia\Pricing\Domain\Enums\PriceListType;
use Purdia\Shared\Traits\BelongsToTenant;

class PriceList extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'type',
        'currency',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PriceListType::class,
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }
}
