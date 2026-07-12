<?php

namespace Purdia\Reference\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Reference\Domain\Models\Unit;

/**
 * @mixin Unit
 */
class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'is_base' => $this->is_base,
            'category' => new UnitCategoryResource($this->whenLoaded('category')),
        ];
    }
}
