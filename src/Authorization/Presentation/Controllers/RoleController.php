<?php

namespace Purdia\Authorization\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Authorization\Application\Actions\CreateRoleAction;
use Purdia\Authorization\Application\Actions\DeleteRoleAction;
use Purdia\Authorization\Application\Actions\SyncPermissionsToRoleAction;
use Purdia\Authorization\Application\Actions\UpdateRoleAction;
use Purdia\Authorization\Application\DTOs\CreateRoleDTO;
use Purdia\Authorization\Application\DTOs\UpdateRoleDTO;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Authorization\Presentation\Requests\CreateRoleRequest;
use Purdia\Authorization\Presentation\Requests\SyncPermissionsRequest;
use Purdia\Authorization\Presentation\Requests\UpdateRoleRequest;
use Purdia\Authorization\Presentation\Resources\V1\RoleResource;
use Purdia\Shared\Support\ApiResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();

        return ApiResponse::success(RoleResource::collection($roles));
    }

    public function store(CreateRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        $dto = new CreateRoleDTO(
            name: $request->validated('name'),
            slug: $request->validated('slug'),
            description: $request->validated('description'),
        );

        $role = $action->execute($dto);

        return ApiResponse::created(new RoleResource($role->load('permissions')));
    }

    public function show(string $role): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($role);

        return ApiResponse::success(new RoleResource($role));
    }

    public function update(UpdateRoleRequest $request, string $role, UpdateRoleAction $action): JsonResponse
    {
        $dto = new UpdateRoleDTO(
            name: $request->validated('name'),
            slug: $request->validated('slug'),
            description: $request->validated('description'),
        );

        $updatedRole = $action->execute($role, $dto);

        return ApiResponse::success(new RoleResource($updatedRole->load('permissions')));
    }

    public function destroy(string $role, DeleteRoleAction $action): JsonResponse
    {
        $action->execute($role);

        return ApiResponse::success(message: 'Role deleted successfully.');
    }

    public function syncPermissions(SyncPermissionsRequest $request, string $role, SyncPermissionsToRoleAction $action): JsonResponse
    {
        $updatedRole = $action->execute($role, $request->validated('permission_ids'));

        return ApiResponse::success(new RoleResource($updatedRole));
    }
}
