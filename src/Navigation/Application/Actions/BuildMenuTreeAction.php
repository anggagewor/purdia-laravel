<?php

namespace Purdia\Navigation\Application\Actions;

use Illuminate\Support\Collection;
use Purdia\Navigation\Domain\Models\Menu;
use Purdia\Shared\Contracts\Authorization\AuthorizationGateway;

class BuildMenuTreeAction
{
    public function __construct(
        private readonly AuthorizationGateway $authorization,
    ) {}

    /**
     * Build the menu tree filtered by user permissions.
     */
    public function execute(string $userId, string $tenantId): Collection
    {
        $menus = Menu::where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('allChildren')
            ->orderBy('sort_order')
            ->get();

        return $this->filterByPermission($menus, $userId);
    }

    private function filterByPermission(Collection $menus, string $userId): Collection
    {
        return $menus->filter(function (Menu $menu) use ($userId) {
            // No permission required — always visible
            if (! $menu->permission) {
                return true;
            }

            return $this->authorization->userCan($userId, $menu->permission);
        })->map(function (Menu $menu) use ($userId) {
            // Recursively filter children
            if ($menu->relationLoaded('allChildren') && $menu->allChildren->isNotEmpty()) {
                $menu->setRelation('allChildren', $this->filterByPermission($menu->allChildren, $userId));
            }

            return $menu;
        })->values();
    }
}
