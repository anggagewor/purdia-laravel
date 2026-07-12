<?php

namespace Purdia\Shared\Exceptions;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $httpStatus = 400,
        public readonly array $context = [],
    ) {
        parent::__construct($message);
    }
}
