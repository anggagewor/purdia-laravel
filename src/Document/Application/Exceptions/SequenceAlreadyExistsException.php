<?php

namespace Purdia\Document\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class SequenceAlreadyExistsException extends DomainException
{
    public function __construct(string $type, ?string $branchId)
    {
        $context = ['type' => $type];
        if ($branchId) {
            $context['branch_id'] = $branchId;
        }

        parent::__construct(
            errorCode: 'DOCUMENT.SEQUENCE_EXISTS',
            message: "A sequence for type '{$type}' already exists" . ($branchId ? " for this branch." : "."),
            httpStatus: 409,
            context: $context,
        );
    }
}
