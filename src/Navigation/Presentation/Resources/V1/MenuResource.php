<?php

namespace Purdia\Navigation\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Navigation\Domain\Models\Menu;

/**
 * @mixin Menu
 */
class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'path' => $this->path,
            'icon' => $this->icon,
            'permission' => $this->permission,
            'sort_order' => $this->sort_order,
            'is_visible' => $this->is_visible,
            'children' => MenuResource::collection($this->whenLoaded('allChildren')),
        ];
    }
}
