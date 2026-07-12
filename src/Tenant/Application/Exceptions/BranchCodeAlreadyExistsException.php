<?php

namespace Purdia\Tenant\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class BranchCodeAlreadyExistsException extends DomainException
{
    public function __construct(string $code)
    {
        parent::__construct(
            errorCode: 'TENANT.BRANCH_CODE_EXISTS',
            message: "A branch with code '{$code}' already exists in this tenant.",
            httpStatus: 409,
            context: ['code' => $code],
        );
    }
}
