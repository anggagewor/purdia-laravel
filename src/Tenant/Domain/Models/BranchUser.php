<?php

namespace Purdia\Tenant\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchUser extends Model
{
    protected $fillable = [
        'tenant_user_id',
        'branch_id',
    ];

    public function tenantUser(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
