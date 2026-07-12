<?php

namespace Purdia\Catalog\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Catalog\Domain\Models\Product;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'type' => $this->type->value,
            'description' => $this->description,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'variants' => VariantResource::collection($this->whenLoaded('variants')),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
