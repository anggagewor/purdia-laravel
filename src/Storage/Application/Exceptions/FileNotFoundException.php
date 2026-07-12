<?php

namespace Purdia\Storage\Application\Exceptions;

use Purdia\Shared\Exceptions\DomainException;

class FileNotFoundException extends DomainException
{
    public function __construct(string $fileId)
    {
        parent::__construct(
            errorCode: 'STORAGE.FILE_NOT_FOUND',
            message: "File with ID {$fileId} not found.",
            httpStatus: 404,
            context: ['file_id' => $fileId],
        );
    }
}
