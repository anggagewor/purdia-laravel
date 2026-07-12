<?php

namespace Purdia\Tenant\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Tenant\Application\Actions\CreateTenantAction;
use Purdia\Tenant\Application\DTOs\CreateTenantDTO;
use Purdia\Tenant\Domain\Models\Tenant;
use Purdia\Tenant\Domain\Models\TenantUser;
use Purdia\Tenant\Presentation\Requests\CreateTenantRequest;
use Purdia\Tenant\Presentation\Resources\V1\TenantResource;
use Purdia\Shared\Support\ApiResponse;

class TenantController extends Controller
{
    /**
     * List tenants the current user belongs to.
     */
    public function index(): JsonResponse
    {
        $userId = (string) request()->user()->id;

        $tenantIds = TenantUser::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('tenant_id');

        $tenants = Tenant::whereIn('id', $tenantIds)
            ->where('is_active', true)
            ->with('branches')
            ->get();

        return ApiResponse::success(TenantResource::collection($tenants));
    }

    public function store(CreateTenantRequest $request, CreateTenantAction $action): JsonResponse
    {
        $dto = new CreateTenantDTO(
            name: $request->validated('name'),
            slug: $request->validated('slug'),
            currency: $request->validated('currency', 'IDR'),
            locale: $request->validated('locale', 'id'),
            timezone: $request->validated('timezone', 'Asia/Jakarta'),
            settings: $request->validated('settings'),
        );

        $tenant = $action->execute($dto);

        // Assign creator as owner (first role, assumed to be 'owner' role)
        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => request()->user()->id,
            'role_id' => 1, // Will be replaced by proper owner role lookup
            'is_active' => true,
        ]);

        return ApiResponse::created(new TenantResource($tenant->load('branches')));
    }

    public function show(string $tenant): JsonResponse
    {
        $tenant = Tenant::with('branches')->findOrFail($tenant);

        return ApiResponse::success(new TenantResource($tenant));
    }
}
