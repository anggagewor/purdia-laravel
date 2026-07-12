<?php

namespace Purdia\Tenant\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class TenantNotFoundException extends DomainException
{
    public function __construct(string $tenantId)
    {
        parent::__construct(
            errorCode: 'TENANT.NOT_FOUND',
            message: "Tenant with ID {$tenantId} not found.",
            httpStatus: 404,
            context: ['tenant_id' => $tenantId],
        );
    }
}
