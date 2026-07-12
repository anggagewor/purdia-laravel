<?php

namespace Purdia\Tenant\Application\Actions;

use Purdia\Tenant\Application\DTOs\CreateBranchDTO;
use Purdia\Tenant\Application\Exceptions\BranchCodeAlreadyExistsException;
use Purdia\Tenant\Domain\Models\Branch;

class CreateBranchAction
{
    public function execute(CreateBranchDTO $dto): Branch
    {
        $exists = Branch::where('tenant_id', $dto->tenantId)
            ->where('code', $dto->code)
            ->exists();

        if ($exists) {
            throw new BranchCodeAlreadyExistsException($dto->code);
        }

        return Branch::create([
            'tenant_id' => $dto->tenantId,
            'parent_branch_id' => $dto->parentBranchId,
            'name' => $dto->name,
            'code' => $dto->code,
            'type' => $dto->type,
            'address' => $dto->address,
            'phone' => $dto->phone,
            'timezone' => $dto->timezone,
            'settings' => $dto->settings,
        ]);
    }
}
