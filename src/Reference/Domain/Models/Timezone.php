<?php

namespace Purdia\Reference\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $fillable = [
        'name',
        'code',
        'offset',
        'utc_offset',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'utc_offset' => 'float',
            'is_active' => 'boolean',
        ];
    }
}
