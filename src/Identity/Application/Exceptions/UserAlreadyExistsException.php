<?php

namespace Purdia\Identity\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(
            errorCode: 'IDENTITY.USER_ALREADY_EXISTS',
            message: "A user with email {$email} already exists.",
            httpStatus: 409,
            context: ['email' => $email],
        );
    }
}
