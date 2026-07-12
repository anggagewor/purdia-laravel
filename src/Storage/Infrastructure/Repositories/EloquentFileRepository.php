<?php

namespace Purdia\Storage\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Models\File;

class EloquentFileRepository implements FileRepository
{
    public function findById(string $id): ?File
    {
        return File::find($id);
    }

    public function findByEntity(string $entityType, string $entityId): Collection
    {
        return File::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByModule(string $module): Collection
    {
        return File::where('module', $module)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): File
    {
        return File::create($data);
    }

    public function delete(string $id): bool
    {
        return File::where('id', $id)->delete() > 0;
    }
}
