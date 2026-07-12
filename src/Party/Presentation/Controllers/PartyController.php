<?php

namespace Purdia\Party\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Party\Application\Actions\CreateOrganizationAction;
use Purdia\Party\Application\Actions\CreatePersonAction;
use Purdia\Party\Application\DTOs\CreateOrganizationDTO;
use Purdia\Party\Application\DTOs\CreatePersonDTO;
use Purdia\Party\Domain\Contracts\PartyRepository;
use Purdia\Party\Presentation\Requests\CreateOrganizationRequest;
use Purdia\Party\Presentation\Requests\CreatePersonRequest;
use Purdia\Party\Presentation\Resources\V1\PartyResource;
use Purdia\Shared\Support\ApiResponse;

class PartyController extends Controller
{
    public function __construct(
        private readonly PartyRepository $parties,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $request->get('search');
        $type = $request->get('type');
        $role = $request->get('role');

        if ($query) {
            $parties = $this->parties->search($query, $type, $role);
        } elseif ($role) {
            $parties = $this->parties->findByRole($role);
        } else {
            $parties = \Purdia\Party\Domain\Models\Party::with(['person', 'organization', 'contacts', 'roles'])
                ->where('is_active', true)
                ->when($type, fn ($q) => $q->where('type', $type))
                ->orderBy('display_name')
                ->paginate(50);

            return ApiResponse::success(PartyResource::collection($parties));
        }

        return ApiResponse::success(PartyResource::collection($parties));
    }

    public function show(string $party): JsonResponse
    {
        $party = $this->parties->findById($party);

        if (! $party) {
            return response()->json(['error' => ['code' => 'PARTY.NOT_FOUND', 'message' => 'Party not found.']], 404);
        }

        return ApiResponse::success(new PartyResource($party));
    }

    public function storePerson(CreatePersonRequest $request, CreatePersonAction $action): JsonResponse
    {
        $dto = new CreatePersonDTO(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            code: $request->validated('code'),
            birthDate: $request->validated('birth_date'),
            gender: $request->validated('gender'),
            religion: $request->validated('religion'),
            bloodType: $request->validated('blood_type'),
            maritalStatus: $request->validated('marital_status'),
            nationalId: $request->validated('national_id'),
            contacts: $request->validated('contacts'),
            addresses: $request->validated('addresses'),
            roles: $request->validated('roles'),
        );

        $party = $action->execute($dto);

        return ApiResponse::created(new PartyResource($party));
    }

    public function storeOrganization(CreateOrganizationRequest $request, CreateOrganizationAction $action): JsonResponse
    {
        $dto = new CreateOrganizationDTO(
            legalName: $request->validated('legal_name'),
            displayName: $request->validated('display_name'),
            code: $request->validated('code'),
            taxNumber: $request->validated('tax_number'),
            npwp: $request->validated('npwp'),
            nib: $request->validated('nib'),
            industry: $request->validated('industry'),
            website: $request->validated('website'),
            contacts: $request->validated('contacts'),
            addresses: $request->validated('addresses'),
            roles: $request->validated('roles'),
        );

        $party = $action->execute($dto);

        return ApiResponse::created(new PartyResource($party));
    }

    public function destroy(string $party): JsonResponse
    {
        $this->parties->delete($party);

        return ApiResponse::success(message: 'Party deleted successfully.');
    }
}
