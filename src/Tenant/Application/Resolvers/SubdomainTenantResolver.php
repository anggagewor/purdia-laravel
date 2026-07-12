<?php

namespace Purdia\Tenant\Application\Resolvers;

use Illuminate\Http\Request;
use Purdia\Tenant\Domain\Models\Tenant;

class SubdomainTenantResolver implements TenantResolverInterface
{
    public function resolve(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Need at least 3 parts: subdomain.domain.tld
        if (count($parts) < 3) {
            return null;
        }

        $subdomain = $parts[0];

        // Skip common non-tenant subdomains
        if (in_array($subdomain, ['www', 'api', 'app', 'admin'])) {
            return null;
        }

        return Tenant::where('slug', $subdomain)
            ->where('is_active', true)
            ->first();
    }
}
