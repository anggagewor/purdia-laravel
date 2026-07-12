<?php

namespace Purdia\Config\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Config\Domain\Enums\ConfigType;

class BulkSetConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'configs' => ['required', 'array', 'min:1'],
            'configs.*.key' => ['required', 'string', 'max:255'],
            'configs.*.value' => ['present'],
            'configs.*.type' => ['sometimes', 'string', Rule::enum(ConfigType::class)],
        ];
    }
}
