<?php

namespace Purdia\Document\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Purdia\Document\Domain\Enums\ResetFrequency;

class Sequence extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'type',
        'prefix',
        'format',
        'current_number',
        'reset_frequency',
        'last_reset_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_number' => 'integer',
            'reset_frequency' => ResetFrequency::class,
            'last_reset_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
