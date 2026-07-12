<?php

namespace Purdia\Tenant\Infrastructure\Gateway;

use Purdia\Shared\Contracts\Tenant\TenantContextInterface;
use Purdia\Tenant\Application\Context\TenantContext;

class TenantContextAdapter implements TenantContextInterface
{
    public function tenantId(): ?int
    {
        return TenantContext::tenantId();
    }

    public function branchId(): ?int
    {
        return TenantContext::branchId();
    }

    public function branchIds(): array
    {
        return TenantContext::branchIds();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return TenantContext::setting($key, $default);
    }

    public function isResolved(): bool
    {
        return TenantContext::isResolved();
    }
}
