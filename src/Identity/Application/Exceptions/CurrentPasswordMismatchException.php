<?php

namespace Purdia\Identity\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class CurrentPasswordMismatchException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: 'IDENTITY.CURRENT_PASSWORD_MISMATCH',
            message: 'The current password is incorrect.',
            httpStatus: 422,
        );
    }
}
