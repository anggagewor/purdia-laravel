<?php

namespace Purdia\Party\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Purdia\Party\Domain\Enums\AddressType;

class Address extends Model
{
    protected $table = 'party_addresses';

    protected $fillable = [
        'party_id',
        'type',
        'label',
        'line_1',
        'line_2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'type' => AddressType::class,
            'is_primary' => 'boolean',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
