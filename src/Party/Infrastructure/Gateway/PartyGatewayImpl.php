<?php

namespace Purdia\Party\Infrastructure\Gateway;

use Purdia\Party\Domain\Contracts\PartyRepository;
use Purdia\Shared\Contracts\Party\PartyGateway;
use Purdia\Shared\DTOs\Party\PartyDTO;

class PartyGatewayImpl implements PartyGateway
{
    public function __construct(
        private readonly PartyRepository $parties,
    ) {}

    public function resolveParty(string $id): ?PartyDTO
    {
        $party = $this->parties->findById($id);

        if (! $party) {
            return null;
        }

        return new PartyDTO(
            id: (string) $party->id,
            type: $party->type->value,
            displayName: $party->display_name,
            code: $party->code,
            roles: $party->roles->pluck('role')->toArray(),
        );
    }

    public function findByRole(string $role): array
    {
        return $this->parties->findByRole($role)->map(fn ($p) => new PartyDTO(
            id: (string) $p->id,
            type: $p->type->value,
            displayName: $p->display_name,
            code: $p->code,
            roles: $p->roles->pluck('role')->toArray(),
        ))->toArray();
    }

    public function findCustomers(): array
    {
        return $this->findByRole('customer');
    }

    public function findSuppliers(): array
    {
        return $this->findByRole('supplier');
    }

    public function findEmployees(): array
    {
        return $this->findByRole('employee');
    }
}
