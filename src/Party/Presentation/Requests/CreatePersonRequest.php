<?php

namespace Purdia\Party\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'religion' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:50'],
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
