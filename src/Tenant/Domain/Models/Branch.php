<?php

namespace Purdia\Tenant\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Purdia\Tenant\Domain\Enums\BranchType;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_branch_id',
        'name',
        'code',
        'type',
        'address',
        'phone',
        'timezone',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => BranchType::class,
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'parent_branch_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Branch::class, 'parent_branch_id');
    }

    public function branchUsers(): HasMany
    {
        return $this->hasMany(BranchUser::class);
    }

    /**
     * Get a setting with inheritance: Branch → Tenant → default.
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings ?? [];

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        // Fallback to tenant setting
        return $this->tenant->setting($key, $default);
    }
}
