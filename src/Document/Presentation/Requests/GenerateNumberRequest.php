<?php

namespace Purdia\Document\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
