<?php

namespace Purdia\Identity\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class InvalidTokenException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: 'IDENTITY.INVALID_TOKEN',
            message: 'The provided token is invalid or has expired.',
            httpStatus: 401,
        );
    }
}
