<?php

namespace Purdia\Tenant\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class TenantSlugAlreadyExistsException extends DomainException
{
    public function __construct(string $slug)
    {
        parent::__construct(
            errorCode: 'TENANT.SLUG_EXISTS',
            message: "A tenant with slug '{$slug}' already exists.",
            httpStatus: 409,
            context: ['slug' => $slug],
        );
    }
}
