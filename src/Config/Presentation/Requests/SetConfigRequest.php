<?php

namespace Purdia\Config\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Purdia\Config\Domain\Enums\ConfigType;

class SetConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['present'],
            'type' => ['sometimes', 'string', Rule::enum(ConfigType::class)],
        ];
    }
}
