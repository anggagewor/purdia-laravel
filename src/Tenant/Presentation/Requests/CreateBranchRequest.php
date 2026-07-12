<?php

namespace Purdia\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Tenant\Domain\Enums\BranchType;

class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/'],
            'type' => ['sometimes', 'string', Rule::enum(BranchType::class)],
            'parent_branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
