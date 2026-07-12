<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'symbol',
        'is_base',
    ];

    protected function casts(): array
    {
        return [
            'is_base' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }

    public function conversionsFrom(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'from_unit_id');
    }

    public function conversionsTo(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'to_unit_id');
    }
}
