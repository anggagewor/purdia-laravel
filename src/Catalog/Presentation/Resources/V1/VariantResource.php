<?php

namespace Purdia\Catalog\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'attributes' => $this->attributes,
            'is_active' => $this->is_active,
        ];
    }
}
