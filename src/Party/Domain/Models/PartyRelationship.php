<?php

namespace Purdia\Party\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Purdia\Party\Domain\Enums\RelationshipType;

class PartyRelationship extends Model
{
    protected $fillable = [
        'party_a_id',
        'party_b_id',
        'type',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => RelationshipType::class,
            'is_active' => 'boolean',
        ];
    }

    public function partyA(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_a_id');
    }

    public function partyB(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_b_id');
    }
}
