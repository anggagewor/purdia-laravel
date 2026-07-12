<?php

namespace Purdia\Authorization\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class RoleNotFoundException extends DomainException
{
    public function __construct(string $roleId)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.ROLE_NOT_FOUND',
            message: "Role with ID {$roleId} not found.",
            httpStatus: 404,
            context: ['role_id' => $roleId],
        );
    }
}
