<?php

namespace Purdia\Storage\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Storage\Domain\Models\File;

/**
 * @mixin File
 */
class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'extension' => $this->extension,
            'visibility' => $this->visibility->value,
            'module' => $this->module,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'metadata' => $this->metadata,
            'url' => $this->generateUrl(),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }

    private function generateUrl(): ?string
    {
        if ($this->resource->isPublic()) {
            return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->path);
        }

        return route('files.download', $this->id);
    }
}
