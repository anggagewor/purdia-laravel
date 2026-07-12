<?php

namespace Purdia\Identity\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class InvalidResetTokenException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: 'IDENTITY.INVALID_RESET_TOKEN',
            message: 'The password reset token is invalid or has expired.',
            httpStatus: 422,
        );
    }
}
