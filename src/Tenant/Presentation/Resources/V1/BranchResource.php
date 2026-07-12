<?php

namespace Purdia\Tenant\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Tenant\Domain\Models\Branch;

/**
 * @mixin Branch
 */
class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'parent_branch_id' => $this->parent_branch_id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'type_icon' => $this->type->icon(),
            'address' => $this->address,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'children' => BranchResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
