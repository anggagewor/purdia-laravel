<?php

namespace Purdia\Document\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Document\Application\Actions\CreateSequenceAction;
use Purdia\Document\Application\DTOs\CreateSequenceDTO;
use Purdia\Document\Domain\Models\Sequence;
use Purdia\Document\Presentation\Requests\CreateSequenceRequest;
use Purdia\Document\Presentation\Requests\GenerateNumberRequest;
use Purdia\Document\Presentation\Resources\V1\SequenceResource;
use Purdia\Shared\Contracts\Document\DocumentEngine;
use Purdia\Shared\Support\ApiResponse;
use Purdia\Tenant\Application\Context\TenantContext;

class SequenceController extends Controller
{
    public function index(): JsonResponse
    {
        $sequences = Sequence::where('tenant_id', TenantContext::tenantId())
            ->orderBy('type')
            ->get();

        return ApiResponse::success(SequenceResource::collection($sequences));
    }

    public function store(CreateSequenceRequest $request, CreateSequenceAction $action): JsonResponse
    {
        $dto = new CreateSequenceDTO(
            tenantId: (string) TenantContext::tenantId(),
            type: $request->validated('type'),
            prefix: $request->validated('prefix'),
            format: $request->validated('format', '{PREFIX}-{BRANCH}-{YYYY}{MM}-{####}'),
            resetFrequency: $request->validated('reset_frequency', 'monthly'),
            branchId: $request->validated('branch_id'),
        );

        $sequence = $action->execute($dto);

        return ApiResponse::created(new SequenceResource($sequence));
    }

    public function show(string $sequence): JsonResponse
    {
        $sequence = Sequence::where('tenant_id', TenantContext::tenantId())
            ->findOrFail($sequence);

        return ApiResponse::success(new SequenceResource($sequence));
    }

    /**
     * Generate the next number for a given document type.
     * POST /api/documents/generate
     */
    public function generate(GenerateNumberRequest $request, DocumentEngine $engine): JsonResponse
    {
        $number = $engine->generate(
            type: $request->validated('type'),
            tenantId: (string) TenantContext::tenantId(),
            branchId: $request->validated('branch_id'),
        );

        return ApiResponse::success([
            'number' => $number,
            'type' => $request->validated('type'),
        ]);
    }

    /**
     * Preview what the next number would look like without consuming it.
     * GET /api/documents/preview?type=INV&branch_id=1
     */
    public function preview(GenerateNumberRequest $request): JsonResponse
    {
        $tenantId = (string) TenantContext::tenantId();
        $branchId = $request->get('branch_id');
        $type = $request->get('type');

        $sequence = Sequence::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(! $branchId, fn ($q) => $q->whereNull('branch_id'))
            ->first();

        if (! $sequence) {
            return ApiResponse::success([
                'next_number' => null,
                'message' => "No sequence configured for type '{$type}'. One will be auto-created on first generate.",
            ]);
        }

        return ApiResponse::success([
            'type' => $type,
            'current_number' => $sequence->current_number,
            'format' => $sequence->format,
            'prefix' => $sequence->prefix,
            'reset_frequency' => $sequence->reset_frequency->value,
        ]);
    }
}
