<?php

namespace Purdia\Tenant\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Application\Resolvers\TenantResolverChain;
use Purdia\Tenant\Domain\Models\TenantUser;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        private readonly TenantResolverChain $resolverChain,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolverChain->resolve($request);

        if (! $tenant) {
            return response()->json([
                'error' => [
                    'code' => 'TENANT.NOT_RESOLVED',
                    'message' => 'Unable to resolve tenant. Please provide X-Tenant-Id header.',
                ],
            ], 400);
        }

        // Verify user belongs to this tenant
        $user = $request->user();
        if ($user) {
            $tenantUser = TenantUser::where('tenant_id', $tenant->id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (! $tenantUser) {
                return response()->json([
                    'error' => [
                        'code' => 'TENANT.ACCESS_DENIED',
                        'message' => 'You do not have access to this tenant.',
                    ],
                ], 403);
            }

            // Set accessible branch IDs
            $branchIds = $tenantUser->branchUsers()->pluck('branch_id')->toArray();
            if (empty($branchIds)) {
                // Full access — all branches
                $branchIds = $tenant->branches()->pluck('id')->toArray();
            }
            TenantContext::setBranchIds($branchIds);
        }

        TenantContext::set($tenant);

        return $next($request);
    }
}
