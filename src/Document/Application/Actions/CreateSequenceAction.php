<?php

namespace Purdia\Document\Application\Actions;

use Purdia\Document\Application\DTOs\CreateSequenceDTO;
use Purdia\Document\Application\Exceptions\SequenceAlreadyExistsException;
use Purdia\Document\Domain\Models\Sequence;

class CreateSequenceAction
{
    public function execute(CreateSequenceDTO $dto): Sequence
    {
        $exists = Sequence::where('tenant_id', $dto->tenantId)
            ->where('type', $dto->type)
            ->when($dto->branchId, fn ($q) => $q->where('branch_id', $dto->branchId))
            ->when(! $dto->branchId, fn ($q) => $q->whereNull('branch_id'))
            ->exists();

        if ($exists) {
            throw new SequenceAlreadyExistsException($dto->type, $dto->branchId);
        }

        return Sequence::create([
            'tenant_id' => $dto->tenantId,
            'branch_id' => $dto->branchId,
            'type' => $dto->type,
            'prefix' => $dto->prefix,
            'format' => $dto->format,
            'current_number' => 0,
            'reset_frequency' => $dto->resetFrequency,
            'is_active' => true,
        ]);
    }
}
