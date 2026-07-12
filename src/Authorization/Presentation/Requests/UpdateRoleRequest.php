<?php

namespace Purdia\Authorization\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,'.$this->route('role')],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
