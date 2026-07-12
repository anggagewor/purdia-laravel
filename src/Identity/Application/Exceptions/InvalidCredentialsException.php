<?php

namespace Purdia\Identity\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: 'IDENTITY.INVALID_CREDENTIALS',
            message: 'The provided credentials are incorrect.',
            httpStatus: 401,
        );
    }
}
