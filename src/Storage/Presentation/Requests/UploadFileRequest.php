<?php

namespace Purdia\Storage\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Storage\Domain\Enums\FileVisibility;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:51200'],
            'module' => ['required', 'string', 'max:100'],
            'entity_type' => ['nullable', 'string', 'max:100'],
            'entity_id' => ['nullable', 'string', 'max:100'],
            'visibility' => ['sometimes', 'string', Rule::enum(FileVisibility::class)],
            'disk' => ['nullable', 'string', 'max:50'],
            'path_prefix' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
