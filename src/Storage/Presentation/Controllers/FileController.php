<?php

namespace Purdia\Storage\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Purdia\Storage\Application\Actions\CheckFileAccessAction;
use Purdia\Storage\Application\Actions\DeleteFileAction;
use Purdia\Storage\Application\Actions\GrantFileAccessAction;
use Purdia\Storage\Application\Actions\RevokeFileAccessAction;
use Purdia\Storage\Application\Actions\UploadFileAction;
use Purdia\Storage\Application\DTOs\GrantAccessDTO;
use Purdia\Storage\Application\DTOs\UploadFileDTO;
use Purdia\Storage\Application\Exceptions\FileAccessDeniedException;
use Purdia\Storage\Application\Exceptions\FileNotFoundException;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Enums\FileAccessLevel;
use Purdia\Storage\Domain\Enums\FileVisibility;
use Purdia\Storage\Presentation\Requests\GrantAccessRequest;
use Purdia\Storage\Presentation\Requests\UploadFileRequest;
use Purdia\Storage\Presentation\Resources\V1\FileResource;
use Purdia\Shared\Support\ApiResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function __construct(
        private readonly FileRepository $files,
    ) {}

    public function upload(UploadFileRequest $request, UploadFileAction $action): JsonResponse
    {
        $dto = new UploadFileDTO(
            module: $request->validated('module'),
            entityType: $request->validated('entity_type'),
            entityId: $request->validated('entity_id'),
            disk: $request->validated('disk'),
            pathPrefix: $request->validated('path_prefix'),
            visibility: FileVisibility::tryFrom($request->validated('visibility', 'private')) ?? FileVisibility::Private,
            metadata: $request->validated('metadata'),
        );

        $file = $action->execute(
            $request->file('file'),
            $dto,
            (string) $request->user()->id,
        );

        return ApiResponse::created(new FileResource($file));
    }

    public function show(string $file): JsonResponse
    {
        $fileModel = $this->files->findById($file);

        if (! $fileModel) {
            throw new FileNotFoundException($file);
        }

        return ApiResponse::success(new FileResource($fileModel));
    }

    public function download(string $file, CheckFileAccessAction $checkAccess, Request $request): StreamedResponse
    {
        $fileModel = $this->files->findById($file);

        if (! $fileModel) {
            throw new FileNotFoundException($file);
        }

        // Check access
        $user = $request->user();
        $roleIds = $user->roles()->pluck('roles.id')->map(fn ($id) => (string) $id)->toArray();
        $access = $checkAccess->execute($file, (string) $user->id, $roleIds);

        if (! $access) {
            throw new FileAccessDeniedException($file);
        }

        return Storage::disk($fileModel->disk)->download($fileModel->path, $fileModel->original_name);
    }

    public function destroy(string $file, DeleteFileAction $action): JsonResponse
    {
        $action->execute($file);

        return ApiResponse::success(message: 'File deleted successfully.');
    }

    public function byEntity(Request $request, string $entityType, string $entityId): JsonResponse
    {
        $files = $this->files->findByEntity($entityType, $entityId);

        return ApiResponse::success(FileResource::collection($files));
    }

    public function grantAccess(GrantAccessRequest $request, string $file, GrantFileAccessAction $action): JsonResponse
    {
        $dto = new GrantAccessDTO(
            fileId: $file,
            accessorType: $request->validated('accessor_type'),
            accessorId: $request->validated('accessor_id'),
            accessLevel: FileAccessLevel::tryFrom($request->validated('access_level', 'read_only')) ?? FileAccessLevel::ReadOnly,
        );

        $action->execute($dto);

        return ApiResponse::success(message: 'Access granted successfully.');
    }

    public function revokeAccess(Request $request, string $file, RevokeFileAccessAction $action): JsonResponse
    {
        $request->validate([
            'accessor_type' => ['required', 'string', 'in:user,role'],
            'accessor_id' => ['required', 'string'],
        ]);

        $action->execute($file, $request->get('accessor_type'), $request->get('accessor_id'));

        return ApiResponse::success(message: 'Access revoked successfully.');
    }
}
