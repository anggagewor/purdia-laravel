<?php

namespace Purdia\Tenant\Application\Context;

use Purdia\Tenant\Domain\Models\Branch;
use Purdia\Tenant\Domain\Models\Tenant;

class TenantContext
{
    private static ?Tenant $tenant = null;
    private static ?Branch $branch = null;
    private static array $branchIds = [];

    public static function set(Tenant $tenant): void
    {
        static::$tenant = $tenant;
    }

    public static function setBranch(Branch $branch): void
    {
        static::$branch = $branch;
    }

    public static function setBranchIds(array $branchIds): void
    {
        static::$branchIds = $branchIds;
    }

    public static function tenant(): ?Tenant
    {
        return static::$tenant;
    }

    public static function tenantId(): ?int
    {
        return static::$tenant?->id;
    }

    public static function branch(): ?Branch
    {
        return static::$branch;
    }

    public static function branchId(): ?int
    {
        return static::$branch?->id;
    }

    public static function branchIds(): array
    {
        return static::$branchIds;
    }

    /**
     * Get a setting with full inheritance: Branch → Tenant → default.
     */
    public static function setting(string $key, mixed $default = null): mixed
    {
        if (static::$branch) {
            return static::$branch->setting($key, $default);
        }

        if (static::$tenant) {
            return static::$tenant->setting($key, $default);
        }

        return $default;
    }

    public static function isResolved(): bool
    {
        return static::$tenant !== null;
    }

    public static function flush(): void
    {
        static::$tenant = null;
        static::$branch = null;
        static::$branchIds = [];
    }
}
