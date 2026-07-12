<?php

namespace Purdia\Storage\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class FileTooLargeException extends DomainException
{
    public function __construct(int $maxSize)
    {
        parent::__construct(
            errorCode: 'STORAGE.FILE_TOO_LARGE',
            message: "File exceeds maximum allowed size of {$maxSize} bytes.",
            httpStatus: 422,
            context: ['max_size' => $maxSize],
        );
    }
}
