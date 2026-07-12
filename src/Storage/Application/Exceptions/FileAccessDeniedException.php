<?php

namespace Purdia\Storage\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class FileAccessDeniedException extends DomainException
{
    public function __construct(string $fileId)
    {
        parent::__construct(
            errorCode: 'STORAGE.ACCESS_DENIED',
            message: "You do not have access to this file.",
            httpStatus: 403,
            context: ['file_id' => $fileId],
        );
    }
}
