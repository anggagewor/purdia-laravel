<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LookupType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(LookupItem::class, 'type_id')->orderBy('sort_order');
    }
}
