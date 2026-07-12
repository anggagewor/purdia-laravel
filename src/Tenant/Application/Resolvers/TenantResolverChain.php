<?php

namespace Purdia\Tenant\Application\Resolvers;

use Illuminate\Http\Request;
use Purdia\Tenant\Domain\Models\Tenant;

class TenantResolverChain
{
    /** @var TenantResolverInterface[] */
    private array $resolvers = [];

    public function addResolver(TenantResolverInterface $resolver): self
    {
        $this->resolvers[] = $resolver;

        return $this;
    }

    /**
     * Try each resolver in order until one succeeds.
     */
    public function resolve(Request $request): ?Tenant
    {
        foreach ($this->resolvers as $resolver) {
            $tenant = $resolver->resolve($request);

            if ($tenant !== null) {
                return $tenant;
            }
        }

        return null;
    }
}
