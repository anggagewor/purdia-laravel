<?php

namespace Purdia\Tenant\Application\Resolvers;

use Illuminate\Http\Request;
use Purdia\Tenant\Domain\Models\Tenant;

interface TenantResolverInterface
{
    /**
     * Attempt to resolve a tenant from the request.
     * Returns null if this resolver cannot determine the tenant.
     */
    public function resolve(Request $request): ?Tenant;
}
