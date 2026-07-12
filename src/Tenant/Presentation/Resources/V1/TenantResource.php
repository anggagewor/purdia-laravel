<?php

namespace Purdia\Tenant\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Tenant\Domain\Models\Tenant;

/**
 * @mixin Tenant
 */
class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
