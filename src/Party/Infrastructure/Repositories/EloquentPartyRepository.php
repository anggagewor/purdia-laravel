<?php

namespace Purdia\Party\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Party\Domain\Contracts\PartyRepository;
use Purdia\Party\Domain\Models\Party;

class EloquentPartyRepository implements PartyRepository
{
    public function findById(string $id): ?Party
    {
        return Party::with(['person', 'organization', 'contacts', 'addresses', 'roles'])->find($id);
    }

    public function findByCode(string $code): ?Party
    {
        return Party::where('code', $code)->first();
    }

    public function findByRole(string $role): Collection
    {
        return Party::whereHas('roles', fn ($q) => $q->where('role', $role)->where('is_active', true))
            ->with(['person', 'organization', 'contacts', 'roles'])
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();
    }

    public function search(string $query, ?string $type = null, ?string $role = null): Collection
    {
        return Party::query()
            ->where('display_name', 'like', "%{$query}%")
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($role, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('role', $role)))
            ->with(['person', 'organization', 'roles'])
            ->where('is_active', true)
            ->orderBy('display_name')
            ->limit(50)
            ->get();
    }

    public function create(array $data): Party
    {
        return Party::create($data);
    }

    public function delete(string $id): bool
    {
        $party = Party::find($id);
        return $party ? (bool) $party->delete() : false;
    }
}
