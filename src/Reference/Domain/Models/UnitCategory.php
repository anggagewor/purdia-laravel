<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'category_id');
    }
}
