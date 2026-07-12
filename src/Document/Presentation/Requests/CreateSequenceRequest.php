<?php

namespace Purdia\Document\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Document\Domain\Enums\ResetFrequency;

class CreateSequenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'prefix' => ['required', 'string', 'max:20'],
            'format' => ['sometimes', 'string', 'max:255'],
            'reset_frequency' => ['sometimes', 'string', Rule::enum(ResetFrequency::class)],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
