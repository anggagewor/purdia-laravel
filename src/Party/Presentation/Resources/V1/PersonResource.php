<?php

namespace Purdia\Party\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date?->toDateString(),
            'gender' => $this->gender,
            'religion' => $this->religion,
            'blood_type' => $this->blood_type,
            'marital_status' => $this->marital_status,
            'national_id' => $this->national_id,
        ];
    }
}
