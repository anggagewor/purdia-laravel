<?php

namespace Purdia\Storage\Application\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Purdia\Storage\Application\DTOs\UploadFileDTO;
use Purdia\Storage\Application\Exceptions\FileTooLargeException;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Contracts\StorageRuleResolver;
use Purdia\Storage\Domain\Models\File;

class UploadFileAction
{
    public function __construct(
        private readonly FileRepository $files,
        private readonly StorageRuleResolver $ruleResolver,
    ) {}

    public function execute(UploadedFile $uploadedFile, UploadFileDTO $dto, string $uploadedBy): File
    {
        $mimeType = $uploadedFile->getMimeType() ?? 'application/octet-stream';
        $extension = $uploadedFile->getClientOriginalExtension();

        // Resolve storage rule
        $rule = $this->ruleResolver->resolve($mimeType, $extension);

        $disk = $dto->disk ?? $rule?->disk ?? config('filesystems.default');
        $pathPrefix = $dto->pathPrefix ?? $rule?->path_prefix ?? $dto->module;
        $visibility = $dto->visibility;

        // Check max size from rule
        if ($rule && $rule->max_size && $uploadedFile->getSize() > $rule->max_size) {
            throw new FileTooLargeException($rule->max_size);
        }

        // Apply default visibility from rule if not explicitly set
        if ($rule && $rule->visibility_default && $dto->visibility->value === 'private') {
            $visibility = \Purdia\Storage\Domain\Enums\FileVisibility::from($rule->visibility_default);
        }

        // Generate unique filename
        $fileName = Str::uuid().'.'.$extension;
        $path = trim($pathPrefix.'/'.$fileName, '/');

        // Store file
        Storage::disk($disk)->put($path, file_get_contents($uploadedFile->getRealPath()));

        // Save metadata
        return $this->files->create([
            'name' => $fileName,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'size' => $uploadedFile->getSize(),
            'extension' => $extension,
            'visibility' => $visibility->value,
            'module' => $dto->module,
            'entity_type' => $dto->entityType,
            'entity_id' => $dto->entityId,
            'uploaded_by' => $uploadedBy,
            'metadata' => $dto->metadata,
        ]);
    }
}
