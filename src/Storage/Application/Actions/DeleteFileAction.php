<?php

namespace Purdia\Storage\Application\Actions;

use Illuminate\Support\Facades\Storage;
use Purdia\Storage\Application\Exceptions\FileNotFoundException;
use Purdia\Storage\Domain\Contracts\FileRepository;

class DeleteFileAction
{
    public function __construct(
        private readonly FileRepository $files,
    ) {}

    public function execute(string $fileId): void
    {
        $file = $this->files->findById($fileId);

        if (! $file) {
            throw new FileNotFoundException($fileId);
        }

        // Delete from disk
        Storage::disk($file->disk)->delete($file->path);

        // Delete metadata
        $this->files->delete($fileId);
    }
}
