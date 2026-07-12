<?php

namespace Purdia\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'unique:tenants,slug', 'regex:/^[a-z0-9\-]+$/'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'locale' => ['sometimes', 'string', 'max:10'],
            'timezone' => ['sometimes', 'string', 'max:50'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
