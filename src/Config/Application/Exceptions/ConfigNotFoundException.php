<?php

namespace Purdia\Config\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class ConfigNotFoundException extends DomainException
{
    public function __construct(string $group, string $key)
    {
        parent::__construct(
            errorCode: 'CONFIG.NOT_FOUND',
            message: "Config '{$group}.{$key}' not found.",
            httpStatus: 404,
            context: ['group' => $group, 'key' => $key],
        );
    }
}
