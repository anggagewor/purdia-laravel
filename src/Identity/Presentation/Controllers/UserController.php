<?php

namespace Purdia\Identity\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Identity\Application\Actions\CreateUserAction;
use Purdia\Identity\Application\Actions\UpdateUserAction;
use Purdia\Identity\Application\DTOs\CreateUserDTO;
use Purdia\Identity\Application\DTOs\UpdateUserDTO;
use Purdia\Identity\Domain\Models\User;
use Purdia\Identity\Presentation\Requests\CreateUserRequest;
use Purdia\Identity\Presentation\Requests\UpdateUserRequest;
use Purdia\Identity\Presentation\Resources\V1\UserResource;
use Purdia\Shared\Support\ApiResponse;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\TenantUser;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = TenantContext::tenantId();

        $userIds = TenantUser::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->pluck('user_id');

        $query = User::whereIn('id', $userIds)
            ->when($request->get('search'), function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');

        $users = $query->paginate($request->get('per_page', 50));

        // Append tenant role info
        $tenantUsers = TenantUser::where('tenant_id', $tenantId)
            ->whereIn('user_id', $users->pluck('id'))
            ->with(['role', 'branchUsers.branch'])
            ->get()
            ->keyBy('user_id');

        $users->getCollection()->transform(function ($user) use ($tenantUsers) {
            $tu = $tenantUsers->get($user->id);
            $user->setAttribute('tenant_role', $tu?->role?->name);
            $user->setAttribute('tenant_role_slug', $tu?->role?->slug);
            $user->setAttribute('branches', $tu?->branchUsers->map(fn ($bu) => [
                'id' => $bu->branch->id,
                'name' => $bu->branch->name,
                'code' => $bu->branch->code,
            ]) ?? []);

            return $user;
        });

        return ApiResponse::success(UserResource::collection($users));
    }

    public function show(string $user): JsonResponse
    {
        $user = User::findOrFail($user);

        $tenantUser = TenantUser::where('tenant_id', TenantContext::tenantId())
            ->where('user_id', $user->id)
            ->with(['role', 'branchUsers.branch'])
            ->first();

        $user->setAttribute('tenant_role', $tenantUser?->role?->name);
        $user->setAttribute('tenant_role_slug', $tenantUser?->role?->slug);
        $user->setAttribute('branches', $tenantUser?->branchUsers->map(fn ($bu) => [
            'id' => $bu->branch->id,
            'name' => $bu->branch->name,
            'code' => $bu->branch->code,
        ]) ?? []);

        return ApiResponse::success(new UserResource($user));
    }

    public function store(CreateUserRequest $request, CreateUserAction $action): JsonResponse
    {
        $dto = new CreateUserDTO(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            roleId: $request->validated('role_id'),
            branchIds: $request->validated('branch_ids'),
        );

        $user = $action->execute($dto);

        return ApiResponse::created(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, string $user, UpdateUserAction $action): JsonResponse
    {
        $dto = new UpdateUserDTO(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            roleId: $request->validated('role_id'),
            branchIds: $request->validated('branch_ids'),
        );

        $updatedUser = $action->execute($user, $dto);

        return ApiResponse::success(new UserResource($updatedUser));
    }

    public function destroy(string $user): JsonResponse
    {
        // Remove from tenant, don't delete the user entirely
        TenantUser::where('tenant_id', TenantContext::tenantId())
            ->where('user_id', $user)
            ->update(['is_active' => false]);

        return ApiResponse::success(message: 'User removed from tenant.');
    }
}
