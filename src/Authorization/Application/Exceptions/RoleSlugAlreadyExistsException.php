<?php

namespace Purdia\Authorization\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class RoleSlugAlreadyExistsException extends DomainException
{
    public function __construct(string $slug)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.ROLE_SLUG_EXISTS',
            message: "A role with slug '{$slug}' already exists.",
            httpStatus: 409,
            context: ['slug' => $slug],
        );
    }
}
