<?php

namespace Purdia\Party\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Party\Domain\Models\Party;

interface PartyRepository
{
    public function findById(string $id): ?Party;

    public function findByCode(string $code): ?Party;

    public function findByRole(string $role): Collection;

    public function search(string $query, ?string $type = null, ?string $role = null): Collection;

    public function create(array $data): Party;

    public function delete(string $id): bool;
}
