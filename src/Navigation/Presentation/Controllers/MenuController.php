<?php

namespace Purdia\Navigation\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Navigation\Application\Actions\BuildMenuTreeAction;
use Purdia\Navigation\Application\Actions\CreateMenuAction;
use Purdia\Navigation\Application\DTOs\CreateMenuDTO;
use Purdia\Navigation\Domain\Models\Menu;
use Purdia\Navigation\Presentation\Requests\CreateMenuRequest;
use Purdia\Navigation\Presentation\Requests\UpdateMenuRequest;
use Purdia\Navigation\Presentation\Resources\V1\MenuResource;
use Purdia\Shared\Support\ApiResponse;
use Purdia\Tenant\Application\Context\TenantContext;

class MenuController extends Controller
{
    /**
     * Get full menu tree (admin — unfiltered).
     */
    public function index(): JsonResponse
    {
        $menus = Menu::where('tenant_id', TenantContext::tenantId())
            ->whereNull('parent_id')
            ->with('allChildren')
            ->orderBy('sort_order')
            ->get();

        return ApiResponse::success(MenuResource::collection($menus));
    }

    /**
     * Get menu tree filtered by current user's permissions.
     * This is what the frontend sidebar renders.
     */
    public function tree(BuildMenuTreeAction $action): JsonResponse
    {
        $userId = (string) request()->user()->id;
        $tenantId = (string) TenantContext::tenantId();

        $menus = $action->execute($userId, $tenantId);

        return ApiResponse::success(MenuResource::collection($menus));
    }

    public function store(CreateMenuRequest $request, CreateMenuAction $action): JsonResponse
    {
        $dto = new CreateMenuDTO(
            name: $request->validated('name'),
            slug: $request->validated('slug'),
            path: $request->validated('path'),
            icon: $request->validated('icon'),
            permission: $request->validated('permission'),
            parentId: $request->validated('parent_id'),
            sortOrder: $request->validated('sort_order', 0),
            isVisible: $request->validated('is_visible', true),
        );

        $menu = $action->execute($dto, (string) TenantContext::tenantId());

        return ApiResponse::created(new MenuResource($menu));
    }

    public function show(string $menu): JsonResponse
    {
        $menu = Menu::where('tenant_id', TenantContext::tenantId())
            ->with('allChildren')
            ->findOrFail($menu);

        return ApiResponse::success(new MenuResource($menu));
    }

    public function update(UpdateMenuRequest $request, string $menu): JsonResponse
    {
        $menu = Menu::where('tenant_id', TenantContext::tenantId())->findOrFail($menu);

        $menu->update($request->validated());

        return ApiResponse::success(new MenuResource($menu->fresh()->load('allChildren')));
    }

    public function destroy(string $menu): JsonResponse
    {
        $menu = Menu::where('tenant_id', TenantContext::tenantId())->findOrFail($menu);

        $menu->delete();

        return ApiResponse::success(message: 'Menu deleted successfully.');
    }

    /**
     * Reorder menus (bulk sort update).
     */
    public function reorder(): JsonResponse
    {
        request()->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
            'items.*.parent_id' => ['nullable', 'integer'],
        ]);

        foreach (request()->get('items') as $item) {
            Menu::where('id', $item['id'])
                ->where('tenant_id', TenantContext::tenantId())
                ->update([
                    'sort_order' => $item['sort_order'],
                    'parent_id' => $item['parent_id'] ?? null,
                ]);
        }

        return ApiResponse::success(message: 'Menu reordered successfully.');
    }
}
