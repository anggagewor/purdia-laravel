<?php

namespace Purdia\Tenant\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Identity\Domain\Models\User;

class TenantUser extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branchUsers(): HasMany
    {
        return $this->hasMany(BranchUser::class);
    }

    /**
     * Check if user has access to all branches (no branch restriction).
     */
    public function hasFullBranchAccess(): bool
    {
        return $this->branchUsers()->count() === 0;
    }
}
