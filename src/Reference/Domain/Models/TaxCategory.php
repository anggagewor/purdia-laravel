<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class TaxCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'rate',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'float',
            'is_active' => 'boolean',
        ];
    }
}
