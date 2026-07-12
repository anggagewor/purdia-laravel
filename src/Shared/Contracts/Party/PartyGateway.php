<?php

namespace Purdia\Shared\Contracts\Party;

use Purdia\Shared\DTOs\Party\PartyDTO;

interface PartyGateway
{
    public function resolveParty(string $id): ?PartyDTO;

    public function findByRole(string $role): array;

    public function findCustomers(): array;

    public function findSuppliers(): array;

    public function findEmployees(): array;
}
