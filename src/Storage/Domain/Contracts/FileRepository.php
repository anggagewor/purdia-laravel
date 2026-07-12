<?php

namespace Purdia\Storage\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Storage\Domain\Models\File;

interface FileRepository
{
    public function findById(string $id): ?File;

    public function findByEntity(string $entityType, string $entityId): Collection;

    public function findByModule(string $module): Collection;

    public function create(array $data): File;

    public function delete(string $id): bool;
}
