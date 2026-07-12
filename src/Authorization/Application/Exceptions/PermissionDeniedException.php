<?php

namespace Purdia\Authorization\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class PermissionDeniedException extends DomainException
{
    public function __construct(string $permission)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.PERMISSION_DENIED',
            message: "You do not have permission: {$permission}.",
            httpStatus: 403,
            context: ['permission' => $permission],
        );
    }
}
