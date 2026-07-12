<?php

namespace Purdia\Navigation\Application\Actions;

use Purdia\Navigation\Application\DTOs\CreateMenuDTO;
use Purdia\Navigation\Domain\Models\Menu;

class CreateMenuAction
{
    public function execute(CreateMenuDTO $dto, string $tenantId): Menu
    {
        return Menu::create([
            'tenant_id' => $tenantId,
            'parent_id' => $dto->parentId,
            'name' => $dto->name,
            'slug' => $dto->slug,
            'path' => $dto->path,
            'icon' => $dto->icon,
            'permission' => $dto->permission,
            'sort_order' => $dto->sortOrder,
            'is_visible' => $dto->isVisible,
        ]);
    }
}
