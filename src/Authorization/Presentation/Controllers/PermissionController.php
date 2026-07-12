<?php

namespace Purdia\Authorization\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Authorization\Application\Actions\CreatePermissionAction;
use Purdia\Authorization\Application\Actions\DeletePermissionAction;
use Purdia\Authorization\Application\Actions\UpdatePermissionAction;
use Purdia\Authorization\Application\DTOs\CreatePermissionDTO;
use Purdia\Authorization\Application\DTOs\UpdatePermissionDTO;
use Purdia\Authorization\Domain\Models\Permission;
use Purdia\Authorization\Presentation\Requests\CreatePermissionRequest;
use Purdia\Authorization\Presentation\Requests\UpdatePermissionRequest;
use Purdia\Authorization\Presentation\Resources\V1\PermissionResource;
use Purdia\Shared\Support\ApiResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return ApiResponse::success(PermissionResource::collection($permissions));
    }

    public function store(CreatePermissionRequest $request, CreatePermissionAction $action): JsonResponse
    {
        $dto = new CreatePermissionDTO(
            name: $request->validated('name'),
            scope: $request->validated('scope'),
            description: $request->validated('description'),
        );

        $permission = $action->execute($dto);

        return ApiResponse::created(new PermissionResource($permission));
    }

    public function show(string $permission): JsonResponse
    {
        $permission = Permission::findOrFail($permission);

        return ApiResponse::success(new PermissionResource($permission));
    }

    public function update(UpdatePermissionRequest $request, string $permission, UpdatePermissionAction $action): JsonResponse
    {
        $dto = new UpdatePermissionDTO(
            name: $request->validated('name'),
            scope: $request->validated('scope'),
            description: $request->validated('description'),
        );

        $updatedPermission = $action->execute($permission, $dto);

        return ApiResponse::success(new PermissionResource($updatedPermission));
    }

    public function destroy(string $permission, DeletePermissionAction $action): JsonResponse
    {
        $action->execute($permission);

        return ApiResponse::success(message: 'Permission deleted successfully.');
    }
}
