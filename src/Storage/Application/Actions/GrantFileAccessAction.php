<?php

namespace Purdia\Storage\Application\Actions;

use Purdia\Storage\Application\DTOs\GrantAccessDTO;
use Purdia\Storage\Application\Exceptions\FileNotFoundException;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Models\FileAccess;

class GrantFileAccessAction
{
    public function __construct(
        private readonly FileRepository $files,
    ) {}

    public function execute(GrantAccessDTO $dto): FileAccess
    {
        $file = $this->files->findById($dto->fileId);

        if (! $file) {
            throw new FileNotFoundException($dto->fileId);
        }

        return FileAccess::updateOrCreate(
            [
                'file_id' => $dto->fileId,
                'accessor_type' => $dto->accessorType,
                'accessor_id' => $dto->accessorId,
            ],
            [
                'access_level' => $dto->accessLevel->value,
            ],
        );
    }
}
