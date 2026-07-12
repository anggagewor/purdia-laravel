<?php

namespace Purdia\Document\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class InvalidTransitionException extends DomainException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct(
            errorCode: 'DOCUMENT.INVALID_TRANSITION',
            message: "Cannot transition from '{$from}' to '{$to}'.",
            httpStatus: 422,
            context: ['from' => $from, 'to' => $to],
        );
    }
}
