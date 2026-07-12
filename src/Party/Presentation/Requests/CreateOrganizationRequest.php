<?php

namespace Purdia\Party\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'legal_name' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:30'],
            'nib' => ['nullable', 'string', 'max:30'],
            'industry' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.type' => ['required', 'string'],
            'contacts.*.value' => ['required', 'string'],
            'contacts.*.label' => ['nullable', 'string'],
            'contacts.*.is_primary' => ['nullable', 'boolean'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.type' => ['required', 'string'],
            'addresses.*.line_1' => ['required', 'string'],
            'addresses.*.city' => ['nullable', 'string'],
            'addresses.*.state' => ['nullable', 'string'],
            'addresses.*.postal_code' => ['nullable', 'string'],
            'addresses.*.country_code' => ['nullable', 'string', 'size:2'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
        ];
    }
}
