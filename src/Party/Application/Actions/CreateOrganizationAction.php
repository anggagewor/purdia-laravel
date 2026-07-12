<?php

namespace Purdia\Party\Application\Actions;

use Illuminate\Support\Facades\DB;
use Purdia\Party\Application\DTOs\CreateOrganizationDTO;
use Purdia\Party\Domain\Enums\PartyType;
use Purdia\Party\Domain\Models\Party;

class CreateOrganizationAction
{
    public function execute(CreateOrganizationDTO $dto): Party
    {
        return DB::transaction(function () use ($dto) {
            $party = Party::create([
                'type' => PartyType::Organization->value,
                'display_name' => $dto->displayName ?? $dto->legalName,
                'code' => $dto->code,
            ]);

            $party->organization()->create([
                'legal_name' => $dto->legalName,
                'tax_number' => $dto->taxNumber,
                'npwp' => $dto->npwp,
                'nib' => $dto->nib,
                'industry' => $dto->industry,
                'website' => $dto->website,
            ]);

            if ($dto->contacts) {
                foreach ($dto->contacts as $contact) {
                    $party->contacts()->create($contact);
                }
            }

            if ($dto->addresses) {
                foreach ($dto->addresses as $address) {
                    $party->addresses()->create($address);
                }
            }

            if ($dto->roles) {
                foreach ($dto->roles as $role) {
                    $party->roles()->create(['role' => $role]);
                }
            }

            return $party->load(['organization', 'contacts', 'addresses', 'roles']);
        });
    }
}
