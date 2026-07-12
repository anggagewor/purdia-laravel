<?php

namespace Purdia\Tenant\Application\Resolvers;

use Illuminate\Http\Request;
use Purdia\Tenant\Domain\Models\Tenant;

class HeaderTenantResolver implements TenantResolverInterface
{
    public function resolve(Request $request): ?Tenant
    {
        $tenantId = $request->header('X-Tenant-Id');

        if (! $tenantId) {
            return null;
        }

        return Tenant::where('id', $tenantId)
            ->where('is_active', true)
            ->first();
    }
}
