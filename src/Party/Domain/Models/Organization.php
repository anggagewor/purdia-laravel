<?php

namespace Purdia\Party\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    protected $fillable = [
        'party_id',
        'legal_name',
        'tax_number',
        'npwp',
        'nib',
        'industry',
        'website',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
