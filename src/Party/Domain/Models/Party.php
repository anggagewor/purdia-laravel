<?php

namespace Purdia\Party\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Purdia\Party\Domain\Enums\PartyType;
use Purdia\Shared\Traits\BelongsToTenant;
use Purdia\Shared\Traits\HasAudit;

class Party extends Model
{
    use BelongsToTenant, HasAudit, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'type',
        'display_name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PartyType::class,
            'is_active' => 'boolean',
        ];
    }

    public function person(): HasOne
    {
        return $this->hasOne(Person::class);
    }

    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(PartyRole::class);
    }

    public function relationshipsAsA(): HasMany
    {
        return $this->hasMany(PartyRelationship::class, 'party_a_id');
    }

    public function relationshipsAsB(): HasMany
    {
        return $this->hasMany(PartyRelationship::class, 'party_b_id');
    }

    public function isPerson(): bool
    {
        return $this->type === PartyType::Person;
    }

    public function isOrganization(): bool
    {
        return $this->type === PartyType::Organization;
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->where('is_active', true)->exists();
    }
}
