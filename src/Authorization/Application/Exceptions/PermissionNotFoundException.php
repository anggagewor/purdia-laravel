<?php

namespace Purdia\Authorization\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class PermissionNotFoundException extends DomainException
{
    public function __construct(string $permissionId)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.PERMISSION_NOT_FOUND',
            message: "Permission with ID {$permissionId} not found.",
            httpStatus: 404,
            context: ['permission_id' => $permissionId],
        );
    }
}
