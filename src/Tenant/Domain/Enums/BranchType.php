<?php

namespace Purdia\Tenant\Domain\Enums;

enum BranchType: string
{
    case Store = 'store';
    case Warehouse = 'warehouse';
    case Office = 'office';
    case Factory = 'factory';
    case Virtual = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::Store => 'Store / Outlet',
            self::Warehouse => 'Warehouse',
            self::Office => 'Office',
            self::Factory => 'Factory',
            self::Virtual => 'Virtual',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Store => 'store',
            self::Warehouse => 'warehouse',
            self::Office => 'building',
            self::Factory => 'factory',
            self::Virtual => 'globe',
        };
    }
}
