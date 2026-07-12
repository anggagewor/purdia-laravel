<?php

namespace Purdia\Storage\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Storage\Domain\Enums\FileAccessLevel;

class GrantAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accessor_type' => ['required', 'string', 'in:user,role'],
            'accessor_id' => ['required', 'string'],
            'access_level' => ['sometimes', 'string', Rule::enum(FileAccessLevel::class)],
        ];
    }
}
