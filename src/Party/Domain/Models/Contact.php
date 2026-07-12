<?php

namespace Purdia\Party\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Purdia\Party\Domain\Enums\ContactType;

class Contact extends Model
{
    protected $table = 'party_contacts';

    protected $fillable = [
        'party_id',
        'type',
        'label',
        'value',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
            'is_primary' => 'boolean',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
