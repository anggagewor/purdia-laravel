<?php

namespace Purdia\Authorization\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class PermissionNameAlreadyExistsException extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.PERMISSION_NAME_EXISTS',
            message: "A permission with name '{$name}' already exists.",
            httpStatus: 409,
            context: ['name' => $name],
        );
    }
}
