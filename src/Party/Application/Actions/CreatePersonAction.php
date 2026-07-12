<?php

namespace Purdia\Party\Application\Actions;

use Illuminate\Support\Facades\DB;
use Purdia\Party\Application\DTOs\CreatePersonDTO;
use Purdia\Party\Domain\Enums\PartyType;
use Purdia\Party\Domain\Models\Party;

class CreatePersonAction
{
    public function execute(CreatePersonDTO $dto): Party
    {
        return DB::transaction(function () use ($dto) {
            $displayName = trim("{$dto->firstName} {$dto->lastName}");

            $party = Party::create([
                'type' => PartyType::Person->value,
                'display_name' => $displayName,
                'code' => $dto->code,
            ]);

            $party->person()->create([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'birth_date' => $dto->birthDate,
                'gender' => $dto->gender,
                'religion' => $dto->religion,
                'blood_type' => $dto->bloodType,
                'marital_status' => $dto->maritalStatus,
                'national_id' => $dto->nationalId,
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

            return $party->load(['person', 'contacts', 'addresses', 'roles']);
        });
    }
}
