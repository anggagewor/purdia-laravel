<?php

namespace Purdia\Reference\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Reference\Domain\Models\UnitCategory;

/**
 * @mixin UnitCategory
 */
class UnitCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'units' => UnitResource::collection($this->whenLoaded('units')),
        ];
    }
}
