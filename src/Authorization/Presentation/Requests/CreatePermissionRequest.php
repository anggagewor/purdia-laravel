<?php

namespace Purdia\Authorization\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Authorization\Domain\Enums\PermissionScope;

class CreatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'scope' => ['required', 'string', Rule::enum(PermissionScope::class)],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
