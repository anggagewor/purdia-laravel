<?php

namespace Purdia\Tenant\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Tenant\Application\Actions\CreateBranchAction;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Application\DTOs\CreateBranchDTO;
use Purdia\Tenant\Domain\Models\Branch;
use Purdia\Tenant\Presentation\Requests\CreateBranchRequest;
use Purdia\Tenant\Presentation\Resources\V1\BranchResource;
use Purdia\Shared\Support\ApiResponse;

class BranchController extends Controller
{
    public function index(): JsonResponse
    {
        $branches = Branch::where('tenant_id', TenantContext::tenantId())
            ->with('children')
            ->whereNull('parent_branch_id')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(BranchResource::collection($branches));
    }

    public function store(CreateBranchRequest $request, CreateBranchAction $action): JsonResponse
    {
        $dto = new CreateBranchDTO(
            tenantId: (string) TenantContext::tenantId(),
            name: $request->validated('name'),
            code: $request->validated('code'),
            type: $request->validated('type', 'store'),
            parentBranchId: $request->validated('parent_branch_id'),
            address: $request->validated('address'),
            phone: $request->validated('phone'),
            timezone: $request->validated('timezone'),
            settings: $request->validated('settings'),
        );

        $branch = $action->execute($dto);

        return ApiResponse::created(new BranchResource($branch));
    }

    public function show(string $branch): JsonResponse
    {
        $branch = Branch::where('tenant_id', TenantContext::tenantId())
            ->with('children')
            ->findOrFail($branch);

        return ApiResponse::success(new BranchResource($branch));
    }
}
