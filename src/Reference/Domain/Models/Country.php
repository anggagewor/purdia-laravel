<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'numeric_code',
        'phone_code',
        'capital',
        'region',
        'subregion',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class);
    }
}
