<?php

namespace Purdia\Party\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'legal_name' => $this->legal_name,
            'tax_number' => $this->tax_number,
            'npwp' => $this->npwp,
            'nib' => $this->nib,
            'industry' => $this->industry,
            'website' => $this->website,
        ];
    }
}
