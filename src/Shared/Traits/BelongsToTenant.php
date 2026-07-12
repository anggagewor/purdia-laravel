<?php

namespace Purdia\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\Tenant;

/**
 * Trait for models that belong to a tenant.
 * Auto-scopes queries and auto-sets tenant_id on create.
 *
 * Usage: add `use BelongsToTenant;` to your model.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-scope all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (TenantContext::isResolved()) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', TenantContext::tenantId());
            }
        });

        // Auto-set tenant_id on create
        static::creating(function ($model) {
            if (TenantContext::isResolved() && ! $model->tenant_id) {
                $model->tenant_id = TenantContext::tenantId();
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Query without tenant scope (for admin/system operations).
     */
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope('tenant');
    }
}
