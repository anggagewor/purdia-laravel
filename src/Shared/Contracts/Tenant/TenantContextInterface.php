<?php

namespace Purdia\Shared\Contracts\Tenant;

/**
 * Interface for modules to access tenant context.
 * Modules MUST use this interface, not the concrete TenantContext class directly.
 */
interface TenantContextInterface
{
    public function tenantId(): ?int;

    public function branchId(): ?int;

    public function branchIds(): array;

    public function setting(string $key, mixed $default = null): mixed;

    public function isResolved(): bool;
}
